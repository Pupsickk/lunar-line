<?php
namespace Database\Factories;
class ReviewFactory extends \Illuminate\Database\Eloquent\Factories\Factory { protected $model=\App\Models\Review::class; public function definition():array {return ['user_id'=>\App\Models\User::factory(),'product_id'=>\App\Models\Product::factory(),'rating'=>fake()->numberBetween(1,5),'comment'=>'Учебный отзыв: '.fake()->sentence(),'status'=>true];} }
