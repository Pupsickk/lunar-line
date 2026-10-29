<?php
namespace Database\Seeders;
use App\Models\{Category,Product,User,Review};
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder {public function run():void {
$cats['self']=Category::firstOrCreate(['slug'=>'self'],['name'=>'О себе']);
$cats['love']=Category::firstOrCreate(['slug'=>'love'],['name'=>'Отношения']);
$cats['work']=Category::firstOrCreate(['slug'=>'work'],['name'=>'Работа']);
Product::updateOrCreate(['slug'=>'one-question'],['category_id'=>$cats['self']->id,'name'=>'Один вопрос','description'=>'Бережный взгляд на один конкретный вопрос. Подойдёт для первого знакомства с форматом.','price'=>790,'image'=>'img/one-question.webp','cards'=>3,'duration'=>15,'status'=>true,'is_hit'=>true,'is_new'=>true,'quantity'=>100]);
Product::updateOrCreate(['slug'=>'relationships'],['category_id'=>$cats['love']->id,'name'=>'Об отношениях','description'=>'Ваши ожидания, границы и общение. Посмотрите на отношения с новой стороны.','price'=>1490,'image'=>'img/relationships.webp','cards'=>7,'duration'=>30,'status'=>true,'is_hit'=>true,'is_new'=>true,'quantity'=>100]);
Product::updateOrCreate(['slug'=>'choice'],['category_id'=>$cats['self']->id,'name'=>'На перекрёстке','description'=>'Два варианта, ваши сомнения и ресурсы. Пространство для осознанного выбора.','price'=>1290,'image'=>'img/choice.webp','cards'=>5,'duration'=>25,'status'=>true,'is_hit'=>true,'is_new'=>true,'quantity'=>100]);
Product::updateOrCreate(['slug'=>'career'],['category_id'=>$cats['work']->id,'name'=>'Дело по душе','description'=>'Вопросы работы, творчества и личных приоритетов. Исследуйте то, что для вас важно.','price'=>1790,'image'=>'img/career.webp','cards'=>9,'duration'=>40,'status'=>true,'is_hit'=>true,'is_new'=>true,'quantity'=>100]);
Product::updateOrCreate(['slug'=>'month'],['category_id'=>$cats['self']->id,'name'=>'Мой месяц','description'=>'Темы ближайшего месяца как повод для рефлексии: отношения, занятия и забота о себе.','price'=>1990,'image'=>'img/month.webp','cards'=>10,'duration'=>45,'status'=>true,'is_hit'=>true,'is_new'=>true,'quantity'=>100]);
Product::updateOrCreate(['slug'=>'deep'],['category_id'=>$cats['self']->id,'name'=>'Большая картина','description'=>'Развёрнутая сессия вокруг вашего запроса с временем на уточняющие вопросы.','price'=>2990,'image'=>'img/deep.webp','cards'=>12,'duration'=>60,'status'=>true,'is_hit'=>true,'is_new'=>true,'quantity'=>100]);
if(!User::where('email','demo@example.test')->exists()){ $u=User::factory()->create(['email'=>'demo@example.test','name'=>'Учебный пользователь']);$u->profile()->create(['phone'=>null]);foreach(Product::all() as $p)Review::factory()->create(['user_id'=>$u->id,'product_id'=>$p->id]);}
}}
