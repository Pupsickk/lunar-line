<?php
namespace App\Models;
class Category extends \Illuminate\Database\Eloquent\Model {use \Illuminate\Database\Eloquent\Factories\HasFactory; protected $fillable=['name','slug','description','parent_id','sort_order']; protected $casts=[]; 
public function products(){return $this->hasMany(Product::class);} public function children(){return $this->hasMany(self::class,'parent_id');} public function parent(){return $this->belongsTo(self::class,'parent_id');}
}
