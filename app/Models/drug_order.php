<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class drug_order extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'drug_id',
        'quantity',
        'price'    
    ];




}
