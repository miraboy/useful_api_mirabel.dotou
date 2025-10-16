<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['buyer_id', 'seller_id', 'product_id', 'quantity', 'total_price', 'status'];
    protected $casts = ['total_price' => 'float'];
}   