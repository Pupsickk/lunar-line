<?php
namespace Database\Factories;
class UserFactory extends \Illuminate\Database\Eloquent\Factories\Factory { protected $model=\App\Models\User::class; public function definition():array {return ['name'=>fake()->name(),'email'=>fake()->unique()->safeEmail(),'password'=>bcrypt('local-test-only'),'role'=>'user'];} }
