<?php
namespace App\Models;
class OrderItem extends \Illuminate\Database\Eloquent\Model {use \Illuminate\Database\Eloquent\Factories\HasFactory; protected $fillable=['order_id','product_id','product_name','quantity','price']; protected $casts=['price'=>'decimal:2','quantity'=>'integer']; 
protected $table='order_product'; public function order(){return $this->belongsTo(Order::class);} public function product(){return $this->belongsTo(Product::class);}
}
