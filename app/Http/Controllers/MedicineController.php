<?php

namespace App\Http\Controllers;

use App\Models\drug;
use App\Models\category; 
use Illuminate\Http\Request;

class MedicineController extends Controller
{

    public function show_by_category(Request $request)
{
        $category_id = $request->category_id;
        $category = category::find($category_id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json($category->drugs);
}

    public function search(Request $request)
{
    $searchTerm = $request->input('search');

    if (empty($searchTerm)) {
        return response()->json([]);
    }
    
    $drugs = drug::where(function($query) use ($searchTerm) {
        $query->where('scientific_name', 'like', "%$searchTerm%")
              ->orWhere('commercial_name', 'like', "%$searchTerm%");
    })
    ->orWhereHas('category', function($query) use ($searchTerm) {
        $query->where('category_name', 'like', "%$searchTerm%");
    })
    ->get();

    return response()->json($drugs);
}

    public function show_details(Request $request)
{
        $medicine_id = $request->id;
        $medicine = drug::with('category')->find($medicine_id); 

        if (!$medicine) {
            return response()->json(['message' => 'Medicine not found'], 404);
        }

        return response()->json($medicine);
}

    public function get_all_categories() 
{
        $categories = category::select('id', 'category_name')->get();       
        return response()->json($categories);
}

}
