<?php

namespace App\Models;

use App\Models\order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class drug extends Model
{
    use HasFactory;

    protected $fillable = [
            'scientific_name',
            'commercial_name',
            'category_id',
            'manufacturer', 
            'quantity',
            'expiry_date',
            'price'
    ];




    public function category(){
        return $this->belongsTo(category::class);
    }

    // public function orders(){
    //     return $this->belongsToMany(order::class,'drug_orders','drug_id','order_id')->withPivot('qauntity');
    // }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'drug_orders', 'drug_id', 'order_id')
                    ->withPivot('quantity', 'price') 
                    ->withTimestamps();
    }
}
