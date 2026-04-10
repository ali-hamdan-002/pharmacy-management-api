<?php

namespace App\Models;

use App\Models\drug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class order extends Model
{
    use HasFactory;


    protected $fillable=[
            'user_id', 
            'status', 
            'payment_status', 
            'total_price' 
                        ];


    // public function drugs(){
    //     return $this->belongsToMany(drug::class,'drug_orders','order_id','drug_id')->withPivot('qauntity');
    // }

    public function drugs()
    {
        return $this->belongsToMany(Drug::class, 'drug_orders', 'order_id', 'drug_id')
                    ->withPivot('quantity') 
                    ->withTimestamps();
    }

    /**
     * علاقة الطلب بالمستخدم (الصيدلية)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
