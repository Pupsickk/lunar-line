<?php
namespace App\Models;
class Product extends \Illuminate\Database\Eloquent\Model {use \Illuminate\Database\Eloquent\Factories\HasFactory; protected $fillable=['category_id','name','slug','description','price','quantity','image','cards','duration','is_new','is_hit','status']; protected $casts=['price'=>'decimal:2','status'=>'boolean','is_new'=>'boolean','is_hit'=>'boolean']; 
public function category(){return $this->belongsTo(Category::class);} public function reviews(){return $this->hasMany(Review::class);} public function orders(){return $this->belongsToMany(Order::class)->withPivot('quantity','price','product_name')->withTimestamps();} public function scopeActive($q){return $q->where('status',true);} public function scopeHits($q){return $q->where('is_hit',true);}
}
