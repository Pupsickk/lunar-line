<?php
namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
class CartController extends Controller {
 public static function rows(Request $r){$cart=$r->session()->get('cart',[]);return Product::active()->whereIn('id',array_keys($cart))->get()->map(fn($p)=>['product'=>$p,'quantity'=>$cart[$p->id]]);}
 public function index(Request $r){return view('cart',['rows'=>self::rows($r)]);}
 public function add(Request $r,Product $product){abort_unless($product->status,404);$cart=$r->session()->get('cart',[]);$cart[$product->id]=min(9,($cart[$product->id]??0)+1);$r->session()->put('cart',$cart);return redirect()->route('cart')->with('success','Расклад добавлен');}
 public function update(Request $r,Product $product){$d=$r->validate(['quantity'=>'required|integer|min:0|max:9']);$cart=$r->session()->get('cart',[]);if($d['quantity']==0)unset($cart[$product->id]);else $cart[$product->id]=$d['quantity'];$r->session()->put('cart',$cart);return back();}
}
