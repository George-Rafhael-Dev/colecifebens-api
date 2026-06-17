<?php

namespace App\Http\Services;

use App\Models\Order;
use App\Models\Product;

class OrderService
{
    private array $statusFlow = [
        'pendente'  => 0,
        'enviado'   => 1,
        'entregue'  => 2,
        'cancelado' => 3,
    ];

    public function getAll(): mixed
    {
        return Order::with('products')->get();
    }

    public function getById(int $id): Order
    {
        $order = Order::with('products')->find($id);
        if (!$order) throw new \Exception('Order not found', 404);
        return $order;
    }

    public function create(array $data): Order
    {
        $total = 0;
        $items = [];

        foreach ($data['products'] as $item) {
            $product = Product::find($item['id']);

            if ($product->user_id === $data['user_id']) {
                throw new \Exception("You cannot purchase your own product: {$product->name}", 422);
            }

            if ($item['quantity'] > $product->stock) {
                throw new \Exception("Insufficient stock for product: {$product->name}|{$product->stock}", 422);
            }

            $total += $product->price * $item['quantity'];
            $items[$product->id] = [
                'quantity'   => $item['quantity'],
                'unit_price' => $product->price,
            ];
        }

        $order = Order::create([
            'user_id'        => $data['user_id'],
            'total'          => $total,
            'payment_method' => $data['payment_method'],
        ]);

        $order->products()->attach($items);

        foreach ($data['products'] as $item) {
            Product::find($item['id'])->decrement('stock', $item['quantity']);
        }

        return $order->load('products');
    }

    public function updateStatus(int $id, array $data): Order
    {
        $order = $this->getById($id);
        $this->validateStatusTransition($order, $data);

        $order->update(array_filter($data, fn($v) => !is_null($v)));

        if (isset($data['payment_status']) && $data['payment_status'] === 'aprovado' && !$order->paid_at) {
            $order->update(['paid_at' => now()]);
        }

        return $order->fresh();
    }

    public function delete(int $id): array
    {
        $order = $this->getById($id);
        $order->delete();
        return ['message' => 'Order deleted'];
    }

    private function validateStatusTransition(Order $order, array $data): void
    {
        $newStatus = $data['status'] ?? $order->status;
        $newPaymentStatus = $data['payment_status'] ?? $order->payment_status;

        if ($order->status === 'cancelado') {
            throw new \Exception('Cannot update a cancelled order', 422);
        }

        if ($order->status === 'entregue' && $newStatus === 'cancelado') {
            throw new \Exception('Cannot cancel a delivered order', 422);
        }

        if (isset($data['status'])) {
            if ($this->statusFlow[$newStatus] < $this->statusFlow[$order->status]) {
                throw new \Exception('Cannot revert order status', 422);
            }
            if ($this->statusFlow[$newStatus] > $this->statusFlow[$order->status] + 1
                && $newStatus !== 'cancelado') {
                throw new \Exception('Cannot skip order status steps', 422);
            }
        }

        if (isset($data['payment_status']) && $data['payment_status'] === 'recusado'
            && $order->payment_status === 'aprovado') {
            throw new \Exception('Cannot revert payment status from approved', 422);
        }

        if (isset($data['payment_status']) && $data['payment_status'] === 'recusado'
            && in_array($order->status, ['enviado', 'entregue'])) {
            throw new \Exception('Cannot refuse payment for a shipped or delivered order', 422);
        }

        if ($newStatus === 'enviado' && $newPaymentStatus !== 'aprovado') {
            throw new \Exception('Cannot ship order without payment confirmation', 422);
        }

        if ($newStatus === 'entregue' && $newPaymentStatus !== 'aprovado') {
            throw new \Exception('Cannot mark order as delivered without approved payment', 422);
        }

        if ($newStatus === 'cancelado' && $newPaymentStatus === 'aprovado') {
            throw new \Exception('Cannot cancel an order with approved payment', 422);
        }

        if (isset($data['payment_method'])
            && $data['payment_method'] !== $order->payment_method
            && ($order->status !== 'pendente' || $order->payment_status !== 'aguardando')) {
            throw new \Exception('Cannot change payment method after order is confirmed or refused', 422);
        }
    }
}