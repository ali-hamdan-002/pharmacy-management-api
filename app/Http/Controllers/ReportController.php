<?php

namespace App\Http\Controllers;

use App\Models\drug;
use App\Models\order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{


   public function admin_show_medicines_report()
{
        $medicines = drug::where('created_at', '>=', Carbon::now()->subDays(7))->get();
        return response()->json($medicines);
}


   public function admin_show_orders_report() 
{
        $orders = order::where('created_at', '>=', Carbon::now()->subDays(7))->get();
        return response()->json($orders);
}


   public function admin_show_orders_value_report() 
{
    $stats = order::where('created_at', '>=', Carbon::now()->subDays(7))
        ->selectRaw('SUM(total_price) as total_sum, MAX(total_price) as max_val')
        ->first();

    $minVal = order::where('created_at', '>=', Carbon::now()->subDays(7))
        ->where('total_price', '>', 0)
        ->min('total_price');

    return response()->json([
        'total_value' => $stats->total_sum ?? 0,
        'max_value' => $stats->max_val ?? 0,
        'min_value' => $minVal ?? 0 
    ]);
}


   public function expired_drugs() 
{
        $expired = drug::whereDate('expiry_date', '<', Carbon::today())->get();
        return response()->json($expired);
}


    public function losses() 
{
        $totalLoss = drug::whereDate('expiry_date', '<', Carbon::today())
            ->get()
            ->sum(function($drug) {
                $qty = isset($drug->quantity) ? $drug->quantity : $drug->qauntity;
                return $qty * $drug->price;
            });

        return response()->json([
            'total_losses_value' => $totalLoss
        ]);
}


    public function user_show_orders_report() 
{
        $userId = Auth::id();
        $orders = order::where('user_id', $userId) 
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->get();
            
        return response()->json($orders);
}


    public function user_show_orders_value_report() 
{
        $total = order::where('user_id', Auth::id())
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->sum('total_price');

        return response()->json([
            'user_total_spent' => $total
        ]);
}


   public function user_show_max_min_orders_value_report() 
{
    $userId = Auth::id();
    $lastWeek = Carbon::now()->subDays(7);
    $maxValue = order::where('user_id', $userId)
        ->where('created_at', '>=', $lastWeek)
        ->max('total_price');
    $minValue = order::where('user_id', $userId)
        ->where('created_at', '>=', $lastWeek)
        ->where('total_price', '>', 0) 
        ->min('total_price');

    return response()->json([
        'user_max_value' => $maxValue ?? 0,
        'user_min_value' => $minValue ?? 0
    ]);
}
}