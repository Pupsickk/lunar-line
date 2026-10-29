<?php
namespace App\Http\Controllers;
use App\Models\{Category,Product};
use Illuminate\Http\Request;
class ShopController extends Controller {
 public function home(){return view('home',['categories'=>Category::withCount('products')->orderBy('sort_order')->get(),'hits'=>Product::active()->hits()->with('category')->take(6)->get(),'newProducts'=>Product::active()->where('is_new',true)->with('category')->take(3)->get()]);}
 public function catalog(Request $r){$d=$r->validate(['category'=>'nullable|string|max:100','min_price'=>'nullable|numeric|min:0','max_price'=>'nullable|numeric|min:0','sort'=>'nullable|in:popular,price_asc,price_desc,new']);$q=Product::active()->with('category');if($r->filled('category'))$q->whereHas('category',fn($q)=>$q->where('slug',$d['category']));foreach(['min_price'=>'>=','max_price'=>'<='] as $k=>$op)if($r->filled($k))$q->where('price',$op,$d[$k]);match($d['sort']??'popular'){'price_asc'=>$q->orderBy('price'),'price_desc'=>$q->orderByDesc('price'),'new'=>$q->latest(),default=>$q->orderByDesc('is_hit')};return view('catalog',['products'=>$q->orderBy('id')->paginate(12)->withQueryString(),'categories'=>Category::all()]);}
 public function product(string $slug){$product=Product::active()->with(['category','reviews'=>fn($q)=>$q->where('status',true)->with('user')])->where('slug',$slug)->firstOrFail();return view('product',['product'=>$product,'similar'=>Product::active()->with('category')->where('category_id',$product->category_id)->whereKeyNot($product->id)->take(4)->get()]);}
}
