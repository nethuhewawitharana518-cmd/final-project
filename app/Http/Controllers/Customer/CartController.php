<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Food;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display session cart items.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('customer.cart', compact('cart'));
    }

    /**
     * Add food item to session cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'food_id'  => 'required|exists:foods,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $food = Food::findOrFail($request->food_id);

        if ($food->available_quantity < $request->quantity) {
            return back()->with('error', 'Requested quantity exceeds available stock.');
        }

        $cart = session()->get('cart', []);

        // Validate single-merchant cart policy
        if (!empty($cart)) {
            $firstItem = reset($cart);
            if ($firstItem['business_id'] !== $food->business_id) {
                return back()->with('error', 'You can only add food items from one store at a time. Please clear your cart first.');
            }
        }

        // Add or update quantity
        if (isset($cart[$food->id])) {
            $newQuantity = $cart[$food->id]['quantity'] + $request->quantity;
            if ($food->available_quantity < $newQuantity) {
                return back()->with('error', 'Adding this quantity exceeds available stock.');
            }
            $cart[$food->id]['quantity'] = $newQuantity;
        } else {
            $cart[$food->id] = [
                'food_id'        => $food->id,
                'business_id'    => $food->business_id,
                'business_name'  => $food->business->business_name,
                'name'           => $food->name,
                'image'          => $food->image,
                'discount_price' => $food->discount_price,
                'original_price' => $food->original_price,
                'quantity'       => (int) $request->quantity,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('customer.cart')->with('success', 'Food item added to cart.');
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $food = Food::findOrFail($id);
        if ($food->available_quantity < $request->quantity) {
            return response()->json(['success' => false, 'message' => 'Requested quantity exceeds stock.'], 400);
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = (int) $request->quantity;
            session()->put('cart', $cart);

            $subtotal = array_sum(array_map(fn($item) => $item['discount_price'] * $item['quantity'], $cart));

            return response()->json([
                'success'  => true,
                'message'  => 'Cart updated.',
                'subtotal' => $subtotal,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Item not in cart.'], 404);
    }

    /**
     * Remove item from cart.
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('customer.cart')->with('success', 'Item removed from cart.');
    }

    /**
     * Clear all items from cart.
     */
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('customer.cart')->with('success', 'Cart cleared.');
    }
}
