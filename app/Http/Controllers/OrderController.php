<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/orders",
     *     summary="List all orders",
     *     tags={"Orders"},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index()
    {
        return response()->json(Order::with('products')->get());
    }
    /**
     * @OA\Get(
     *     path="/orders/{id}",
     *     summary="Show order",
     *     tags={"Orders"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(int $id)
    {
        $order = Order::with('products')->find($id);

        if (!$order) return response()->json(['message' => 'Order not found'], 404);

        return response()->json($order);
    }
    /**
     * @OA\Post(
     *     path="/orders",
     *     summary="Create order",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id","payment_method","products"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="payment_method", type="string", enum={"pix","cartao_credito","boleto"}, example="pix"),
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", example=2)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation error or insufficient stock")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'             => 'required|integer|exists:users,id',
            'payment_method'      => 'required|string|in:pix,cartao_credito,boleto',
            'products'            => 'required|array|min:1',
            'products.*.id'       => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $total = 0;
        $items = [];

        foreach ($request->products as $item) {
            $product = Product::find($item['id']);
            
            if ($product->user_id === $request->user_id) {
                return response()->json([
                    'message' => "You cannot purchase your own product: {$product->name}"
                ], 422);
            }
            if ($item['quantity'] > $product->stock) {
                return response()->json([
                    'message' => "Insufficient stock for product: {$product->name}",
                    'available' => $product->stock
                ], 422);
            }
            $total += $product->price * $item['quantity'];
            $items[$product->id] = [
                'quantity'   => $item['quantity'],
                'unit_price' => $product->price,
            ];
        }

        $order = Order::create([
            'user_id'        => $request->user_id,
            'total'          => $total,
            'payment_method' => $request->payment_method,
        ]);

        $order->products()->attach($items);

        foreach ($request->products as $item) {
            Product::find($item['id'])->decrement('stock', $item['quantity']);
        }

        return response()->json($order->load('products'), 201);
    }
    /**
     * @OA\Patch(
     *     path="/orders/{id}",
     *     summary="Update order status",
     *     tags={"Orders"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", enum={"pendente","enviado","entregue","cancelado"}, example="enviado"),
     *             @OA\Property(property="payment_status", type="string", enum={"aguardando","aprovado","recusado"}, example="aprovado"),
     *             @OA\Property(property="payment_method", type="string", enum={"pix","cartao_credito","boleto"}, example="pix")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=422, description="Business rule violation"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function updateStatus(Request $request, int $id)
    {
        $order = Order::find($id);

        if (!$order) return response()->json(['message' => 'Order not found'], 404);

        $fields = $request->only(['status', 'payment_status', 'payment_method']);
        if (empty($fields)) {
            return response()->json(['message' => 'No fields provided'], 422);
        }

        $request->validate([
            'status'         => 'sometimes|string|in:pendente,enviado,entregue,cancelado',
            'payment_status' => 'sometimes|string|in:aguardando,aprovado,recusado',
            'payment_method' => 'sometimes|string|in:pix,cartao_credito,boleto',
        ]);

        $statusFlow = ['pendente' => 0, 'enviado' => 1, 'entregue' => 2, 'cancelado' => 3];
        $newStatus = $request->status ?? $order->status;
        $newPaymentStatus = $request->payment_status ?? $order->payment_status;

        if ($order->status === 'cancelado') {
            return response()->json(['message' => 'Cannot update a cancelled order'], 422);
        }

        if ($order->status === 'entregue' && $request->status === 'cancelado') {
            return response()->json(['message' => 'Cannot cancel a delivered order'], 422);
        }

        if ($request->status && isset($statusFlow[$request->status])) {
            if ($statusFlow[$request->status] < $statusFlow[$order->status]) {
                return response()->json(['message' => 'Cannot revert order status'], 422);
            }
            if ($statusFlow[$request->status] > $statusFlow[$order->status] + 1 && $request->status !== 'cancelado') {
                return response()->json(['message' => 'Cannot skip order status steps'], 422);
            }
        }

        if ($request->payment_status === 'recusado' && $order->payment_status === 'aprovado') {
            return response()->json(['message' => 'Cannot revert payment status from approved'], 422);
        }

        if ($request->payment_status === 'recusado' && in_array($order->status, ['enviado', 'entregue'])) {
            return response()->json(['message' => 'Cannot refuse payment for a shipped or delivered order'], 422);
        }

        if ($newStatus === 'enviado' && $newPaymentStatus !== 'aprovado') {
            return response()->json(['message' => 'Cannot ship order without payment confirmation'], 422);
        }

        if ($newStatus === 'entregue' && $newPaymentStatus !== 'aprovado') {
            return response()->json(['message' => 'Cannot mark order as delivered without approved payment'], 422);
        }

        if ($newStatus === 'cancelado' && $newPaymentStatus === 'aprovado') {
            return response()->json(['message' => 'Cannot cancel an order with approved payment'], 422);
        }

        if ($request->payment_method && 
            $request->payment_method !== $order->payment_method &&
            ($order->status !== 'pendente' || $order->payment_status !== 'aguardando')) {
            return response()->json(['message' => 'Cannot change payment method after order is confirmed or refused'], 422);
        }

        $order->update($request->only(['status', 'payment_status', 'payment_method']));

        if ($request->payment_status === 'aprovado' && !$order->paid_at) {
            $order->update(['paid_at' => now()]);
        }

        return response()->json($order);
    }
    /**
     * @OA\Delete(
     *     path="/orders/{id}",
     *     summary="Delete order",
     *     tags={"Orders"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy(int $id)
    {
        $order = Order::find($id);

        if (!$order) return response()->json(['message' => 'Order not found'], 404);

        $order->delete();

        return response()->json(['message' => 'Order deleted']);
    }
}