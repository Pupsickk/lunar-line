<?php
namespace Database\Factories;
class CategoryFactory extends \Illuminate\Database\Eloquent\Factories\Factory { protected $model=\App\Models\Category::class; public function definition():array {return ['name'=>fake()->words(2,true),'slug'=>fake()->unique()->uuid(),'description'=>fake()->sentence()];} }
