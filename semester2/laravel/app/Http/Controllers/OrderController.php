<?php
namespace App\Http\Controllers;
use App\Models\{Order,Product};
use App\Http\Requests\CheckoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
class OrderController extends Controller {
 public function checkout(Request $r){$rows=CartController::rows($r);if($rows->isEmpty())return redirect()->route('cart');$key=$r->session()->get('checkout_key',(string)Str::uuid());$r->session()->put('checkout_key',$key);return view('checkout',compact('rows','key'));}
 public function store(CheckoutRequest $r){$d=$r->validated();abort_unless(hash_equals((string)$r->session()->get('checkout_key',''),$d['checkout_key']),419);$cart=$r->session()->get('cart',[]);if(!$cart)throw ValidationException::withMessages(['cart'=>'Корзина пуста']);$token=Str::random(64);
 $order=DB::transaction(function()use($cart,$d,$token){$products=Product::active()->whereIn('id',array_keys($cart))->lockForUpdate()->get();if($products->count()!==count($cart))throw ValidationException::withMessages(['cart'=>'Одна из услуг недоступна. Вернитесь в корзину.']);$sum=0;$lines=[];foreach($products as $p){$q=$cart[$p->id];if(!is_int($q)||$q<1||$q>9||$p->quantity<$q)throw ValidationException::withMessages(['cart'=>'Недостаточно доступных сессий']);$cents=(int)round((float)$p->price*100);$sum+=$cents*$q;$lines[$p->id]=['quantity'=>$q,'price'=>$p->price,'product_name'=>$p->name];}if(Order::where('checkout_key',$d['checkout_key'])->exists())throw ValidationException::withMessages(['cart'=>'Этот заказ уже оформлен']);$order=Order::create(['order_number'=>(string)Str::uuid(),'access_token_hash'=>hash('sha256',$token),'checkout_key'=>$d['checkout_key'],'customer_name'=>$d['name'],'email'=>$d['email'],'phone'=>$d['phone']??null,'notes'=>$d['notes']??null,'total_amount'=>number_format($sum/100,2,'.',''),'status'=>'new']);$order->products()->attach($lines);foreach($products as $p)$p->decrement('quantity',$cart[$p->id]);return $order;});
 $r->session()->forget(['cart','checkout_key']);return redirect()->route('orders.show',['number'=>$order->order_number,'token'=>$token]);}
 public function show(Request $r,string $number){$order=Order::where('order_number',$number)->firstOrFail();abort_unless(hash_equals($order->access_token_hash,hash('sha256',(string)$r->query('token'))),404);return response()->view('order',['order'=>$order->load('products')])->header('Cache-Control','no-store')->header('Referrer-Policy','no-referrer');}
 public function admin(){return view('admin',['orders'=>Order::with('products')->latest()->paginate(20)]);}
}
