<?php

namespace App\Http\Controllers;

use App\Models\drug;
use App\Models\order;
use App\Models\category;
use App\Models\medicine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMedicinController extends Controller
{

    public function __construct()
    {
       // مشان امنع اليوزير يسجل دخول ع هالقصص
     //  $this->middleware('check'|'auth');
    }


    public function admin_add_medicine(Request $request) 
{
        $this->validate($request, [
            'scientific_name' => 'required',
            'commercial_name' => 'required',
            'category' => 'required',
            'manufacturer' => 'required',
            'quantity' => 'required|integer|min:1',
            'expiry_date' => 'required|date',
            'price' => 'required|numeric',
        ]);

        $category_row = category::where('category_name', $request->category)->first();
        if (!$category_row) {
            return response()->json(['message' => 'category not found.'], 404);
        }

        if (Carbon::parse($request->expiry_date)->isPast()) {
            return response()->json(['message' => 'Expiry date has passed!'], 400);
        }

        $existing_drug = drug::where('scientific_name', $request->scientific_name)
                            ->where('expiry_date', $request->expiry_date)
                            ->first();

        if ($existing_drug) {
            $existing_drug->increment('quantity', $request->quantity); 
            return response()->json([
                'message' => 'The medication was previously added with the same expiry date; only the quantity has been updated.'
            ], 200);
        }

        drug::create([
            'scientific_name'     => $request->scientific_name,
            'commercial_name'     => $request->commercial_name,
            'category_id'         => $category_row->id,
            'manufacturer'        => $request->manufacturer,
            'quantity'            => $request->quantity,
            'expiry_date'         => $request->expiry_date,
            'price'               => $request->price,
        ]);

        return response()->json([
        'message' => 'The medication was successfully added.'
        ], 201);
}

    public function show_order()
{
    $all_orders = Order::with(['user:id,name', 'drugs:id,commercial_name,price'])
                      ->latest()
                      ->get();

    return response()->json([
        'status' => true,
        'message' => 'List of all user requests',
        'count' => $all_orders->count(),
        'data' => $all_orders
    ]);
}

    public function admin_sent_order(Request $request) 
{
        $order = order::with('drugs')->find($request->id);

        if (!$order) {
            return response()->json(['message' => 'Request not found'], 404);
        }
        if (in_array($order->status, ['shipped', 'delivered', 'canceled'])) {
            return response()->json([
                'message' => 'This request cannot be modified, the current status is:' . $order->status
            ]);
        }

        $totalPrice = 0;
        $canSend = true;
        $insufficientItems = [];

        foreach ($order->drugs as $drug) {
            $requestedQty = (int) $drug->pivot->quantity; 
            $availableQty = isset($drug->quantity) ? (int)$drug->quantity : (int)$drug->qauntity;
            $unitPrice = (float) $drug->price;

            if ($availableQty < $requestedQty) {
                $canSend = false;
                $insufficientItems[] = $drug->commercial_name; 
                break; 
            }
            $totalPrice += ($unitPrice * $requestedQty);
        }

        if (!$canSend) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient stock for the following medicines:' . implode(', ', $insufficientItems)
            ], 400);
        }

        try {
            DB::transaction(function () use ($order, $totalPrice) {
                
                $order->update([
                    'status' => 'shipped',
                    'total_price' => $totalPrice
                ]);

                foreach ($order->drugs as $drug) {
                    $qtyToDecrement = (int) $drug->pivot->quantity;
                    
                    if ($qtyToDecrement > 0) {
                        $stockColumn = isset($drug->quantity) ? 'quantity' : 'qauntity';
                        $drug->decrement($stockColumn, $qtyToDecrement);
                    }
                }
            });

            return response()->json([
                'status' => true,
                'message' => 'Order shipped successfully and stock updated',
                'data' => [
                    'order_id' => $order->id,
                    'total_price' => $totalPrice,
                    'status' => 'shipped'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'A technical error occurred:' . $e->getMessage()
            ], 500);
        }
}

    public function admin_received_order(Request $request) 
{
        $order = order::find($request->id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }
        if ($order->status === 'delivered') {
            return response()->json([
                'status' => true,
                'message' => 'Order is already marked as delivered.'
            ]);
        }

        if ($order->status !== 'shipped') {
            return response()->json([
                'status' => false,
                'message' => 'Cannot mark as received. The order must be "shipped" first. Current status: ' . $order->status
            ], 400);
        }

        $order->update([
            'status' => 'delivered'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order status has been successfully updated to: Delivered'
        ]);
}

    public function admin_cancel_order(Request $request) 
{
        $order = order::find($request->id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        if ($order->status === 'canceled') {
            return response()->json([
                'status' => true,
                'message' => 'This order is already canceled.'
            ]);
        }

        if (in_array($order->status, ['shipped', 'delivered'])) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot cancel the order because it has already been ' . $order->status . '. Action is irreversible.'
            ], 400);
        }

        $order->update([
            'status' => 'canceled'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'The order has been successfully canceled.'
        ]);
}

    public function admin_paid_order(Request $request) 
{
        $order = order::find($request->id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }
        if ($order->payment_status === 'paid') {
            return response()->json([
                'status' => true,
                'message' => 'This order is already marked as paid.'
            ]);
        }

        if (!in_array($order->status, ['shipped', 'delivered'])) {
            return response()->json([
                'status' => false,
                'message' => 'Payment cannot be processed. The order must be "shipped" or "delivered" first. Current order status: ' . $order->status
            ], 400);
        }

        $order->update([
            'payment_status' => 'paid'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'The payment status has been successfully updated to: Paid',
            'order_details' => [
                'id' => $order->id,
                'status' => $order->status,
                'payment_status' => 'paid'
            ]
        ]);
}


}
