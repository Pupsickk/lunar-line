<?php
namespace Database\Factories;
class OrderFactory extends \Illuminate\Database\Eloquent\Factories\Factory { protected $model=\App\Models\Order::class; public function definition():array {return ['user_id'=>\App\Models\User::factory(),'order_number'=>(string)\Illuminate\Support\Str::uuid(),'access_token_hash'=>hash('sha256',fake()->uuid()),'checkout_key'=>(string)\Illuminate\Support\Str::uuid(),'customer_name'=>fake()->name(),'email'=>fake()->safeEmail(),'total_amount'=>0,'status'=>'new'];} }
