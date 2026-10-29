<?php
namespace Database\Factories;
class ProductFactory extends \Illuminate\Database\Eloquent\Factories\Factory { protected $model=\App\Models\Product::class; public function definition():array {return ['category_id'=>\App\Models\Category::factory(),'name'=>fake()->words(2,true),'slug'=>fake()->unique()->uuid(),'description'=>fake()->paragraph(),'price'=>fake()->numberBetween(500,3000),'quantity'=>100,'image'=>'img/one-question.webp','status'=>true];} }
