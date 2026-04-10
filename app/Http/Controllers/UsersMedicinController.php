<?php

namespace App\Http\Controllers;


use App\Models\drug;
use App\Models\order;
use App\Models\category;
use App\Models\drug_order;
use App\Models\favourite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class UsersMedicinController extends Controller
{

    public function be_order(Request $request) 
{
    $medicines = $request->input('medicine'); 
    $quantity_check = 0;

    foreach($medicines as $medicine) {
        $drug = drug::find($medicine['id']);
        

        if (!$drug || $drug->quantity < $medicine['quantity']) { 
            $quantity_check = 1;
            break;
        }
    }

    if ($quantity_check == 0) {
        $order = order::create([
            'user_id' => Auth::user()->id,
        ]);

        foreach($medicines as $medicine) {
            drug_order::create([
                'order_id' => $order->id,
                'drug_id'  => $medicine['id'],
                'quantity' => $medicine['quantity']
            ]);

     
            $drug = drug::find($medicine['id']);
            $drug->decrement('quantity', $medicine['quantity']);
        }

        return response()->json([
            'message' => 'Order created successfully'
        ], 201);

    } else {
        return response()->json([
            'message' => 'Order not created: Some medicines are out of stock or quantity is insufficient'
        ], 400);
    }
}

    public function show_order()
{
    $id = Auth::user()->id;
    $show = order::where('user_id', $id)->get();            
      return response()->json($show);
}
    public function select_show_order(Request $request)
{
     $id = $request->id;
     $order = order::with('drugs')->find($id); 
     
     if (!$order) {
         return response()->json(['message' => 'Order not found'], 404);
     }

     return response()->json([
         'order_id' => $order->id,
         'status' => $order->status,
         'items' => $order->drugs->map(function($drug) {
             return [
                 'name' => $drug->commercial_name,
                 'unit_price' => $drug->price,
                 'requested_quantity' => $drug->pivot->quantity,
                 "expiry_date"=>$drug->expiry_date,
             ];
         })
     ]);
}

    public function add_favourite(Request $request)
{
        $request->validate([
           'drug_id' => 'required|exists:drugs,id'
        ]);

        $user_id = Auth::user()->id;
        $drug_id = $request->drug_id;

        $fav = favourite::firstOrCreate(
            [
                'user_id'         => $user_id,
                'drug_id'         => $drug_id
            ],
            [
                'status'          => true
            ]
        );

        return response()->json([
            'message' => 'The medication has been successfully added to favorites.',
            'data'    => $fav
        ], 200);
}

    public function get_favourite(Request $request)
{
    $user = auth::user();
    $favorites = $user->favourites()->with('drug')->get();
    $customList = $favorites->map(function ($fav) {
        return [
            'favourite_id' => $fav->id,
            'drug_info'    => [
                'name'  => $fav->drug->commercial_name,
                'price' => $fav->drug->price,          
            ]
        ];
    });

    return response()->json([
        'user_id' => $user->id,
        'list'    => $customList
    ]);
}

    public function delete_favourite(Request $request)
{
    $request->validate([
        'drug_id' => 'required|exists:drugs,id'
    ]);

    $user_id = auth()->user()->id;
    $drug_id = $request->drug_id;

    $deleted = favourite::where('user_id', $user_id)
                        ->where('drug_id', $drug_id)
                        ->delete();

    if ($deleted) {
        return response()->json(['message' => 'The medication has been removed from favorites.']);
    }

    return response()->json(['message' => 'The medication is not even in the favorites list.'], 404);
}


}










