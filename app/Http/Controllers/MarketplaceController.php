<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Order;

class MarketplaceController extends Controller
{
    public function createProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0'
        ]);

        $product = Product::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'min_stock' => $request->min_stock
        ]);

        return response()->json($product->makeHidden(['updated_at']), 201);
    }

    public function getProducts()
    {
        $products = Product::select('id', 'name', 'price', 'stock', 'min_stock')->get();
        return response()->json($products, 200);
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return response()->json(['error' => 'Insufficient stock'], 400);
        }

        $order = DB::transaction(function () use ($product, $request) {
            $product->decrement('stock', $request->quantity);

            return Order::create([
                'buyer_id' => auth()->id(),
                'seller_id' => $product->user_id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'total_price' => $product->price * $request->quantity,
                'status' => 'success'
            ]);
        });

        return response()->json([
            'order_id' => $order->id,
            'buyer_id' => $order->buyer_id,
            'seller_id' => $order->seller_id,
            'product_id' => $order->product_id,
            'quantity' => $order->quantity,
            'total_price' => $order->total_price,
            'status' => $order->status,
            'created_at' => $order->created_at->toIso8601ZuluString()
        ], 201);
    }

    public function restock(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($id);

        $product->increment('stock', $request->quantity);

        return response()->json([
            'product_id' => $product->id,
            'new_stock' => $product->stock,
            'restocked_quantity' => $request->quantity
        ], 200);
    }
}