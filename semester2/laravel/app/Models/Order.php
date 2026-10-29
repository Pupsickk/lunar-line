<?php
namespace App\Models;
class Order extends \Illuminate\Database\Eloquent\Model {use \Illuminate\Database\Eloquent\Factories\HasFactory; protected $fillable=['user_id','order_number','access_token_hash','checkout_key','customer_name','email','phone','notes','total_amount','status','payment_method']; protected $casts=['total_amount'=>'decimal:2']; protected $hidden=['access_token_hash','checkout_key'];
public function user(){return $this->belongsTo(User::class);} public function products(){return $this->belongsToMany(Product::class)->withPivot('quantity','price','product_name')->withTimestamps();} public function items(){return $this->hasMany(OrderItem::class);}
}
