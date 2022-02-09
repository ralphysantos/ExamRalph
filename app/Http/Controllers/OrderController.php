<?php

namespace App\Http\Controllers;
use Auth;
use App\Product;
use App\Order;
use App\OrderDetails;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function store(Request $request){
        try {
            $auth_user = Auth::user();
            if(count($request->order) > 0){
                foreach ($request->order as $order) {
                    $product = Product::find($order['product_id']);
                    if($product){
                        if($order['quantity'] > $product->available_stock){
                            return response()->json(['message' => 'Failed to order this product ('.$product->name.') due to unavailability of the stock'],400);
                        }
                    }else{
                        return response()->json(['message' => 'Product not Found'],400);
                    }
                }

                $new_order = new Order;
                $new_order->user_id = $auth_user->id;
                if($new_order->save()){
                    foreach ($request->order as $order) {
                        $product = Product::find($order['product_id']);
                        if($product){
                            $new_order_details = new OrderDetails;
                            $new_order_details->order_id = $new_order->id;
                            $new_order_details->product_id = $product->id;
                            $new_order_details->quantity = $order['quantity'];
                            
                            if($new_order_details->save()){
                                $remaining_stock = $product->available_stock - $new_order_details->quantity;
                                $product->available_stock = $remaining_stock;
                                $product->save();
                            }
                        }
                    }
                    return response()->json(['message' => 'You have successfully ordered this product/s'],200);
                }
            }else{

                return response()->json(['message' => 'No order avaiable'],400);
            }
        } catch (\Throwable $ex) {
            return response()->json(['message' => 'Error'],400);
        }
    }
}
