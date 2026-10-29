<?php
namespace App\Models;
class Profile extends \Illuminate\Database\Eloquent\Model {use \Illuminate\Database\Eloquent\Factories\HasFactory; protected $fillable=['user_id','phone']; protected $casts=[]; 
public function user(){return $this->belongsTo(User::class);}
}
