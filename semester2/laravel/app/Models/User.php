<?php
namespace App\Models;
class User extends \Illuminate\Foundation\Auth\User {use \Illuminate\Database\Eloquent\Factories\HasFactory; protected $fillable=['name','email','password']; protected $casts=['password'=>'hashed']; protected $hidden=['password','remember_token'];
public function orders(){return $this->hasMany(Order::class);} public function reviews(){return $this->hasMany(Review::class);} public function profile(){return $this->hasOne(Profile::class);}
}
