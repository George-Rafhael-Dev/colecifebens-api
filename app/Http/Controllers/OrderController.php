<?php

namespace App\Http\Controllers;

use App\Http\Services\OrderService;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;

class OrderController extends Controller
{
    public function __construct(private OrderService $service) {}
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
        return response()->json($this->service->getAll());    }
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
        try {
            return response()->json($this->service->getById($id));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
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
    public function store(StoreOrderRequest $request)
    {
        try {
            return response()->json($this->service->create($request->validated()), 201);
        } catch (\Exception $e) {
            [$message, $stock] = explode('|', $e->getMessage()) + [1 => null];
            return response()->json(['message' => $message, 'available' => $stock], 404);
        }
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
    public function updateStatus(UpdateOrderStatusRequest $request, int $id)
    {
        try {
            return response()->json($this->service->updateStatus($id, $request->validated()));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
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
        try {
            return response()->json($this->service->delete($id));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
}