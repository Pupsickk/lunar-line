@extends('layouts.app')
@section('title','Оформление заказа — Лунная линия')
@section('content')
<div class="page-intro"><p class="eyebrow">Последний шаг</p><h1>Оформление заказа</h1><p>Заказ сохранится в базе этого сайта. Оплата пока не подключена — деньги не списываются.</p></div>
<div class="contacts__grid">
<form class="server-form" action="{{route('orders.store')}}" method="post">
    @csrf<input type="hidden" name="checkout_key" value="{{$key}}">
    <label for="name">Имя *</label><input id="name" name="name" autocomplete="name" required minlength="2" maxlength="80" value="{{old('name')}}">
    <label for="email">Email *</label><input id="email" name="email" type="email" autocomplete="email" required maxlength="160" value="{{old('email')}}">
    <label for="phone">Телефон</label><input id="phone" name="phone" type="tel" autocomplete="tel" maxlength="20" value="{{old('phone')}}">
    <label for="notes">Ваш вопрос</label><textarea id="notes" name="notes" rows="4" maxlength="2000">{{old('notes')}}</textarea>
    <label for="agree"><input id="agree" type="checkbox" name="agree" value="1" required @checked(old('agree'))> Согласен с <a href="{{route('privacy')}}">обработкой данных</a></label>
    <button class="btn">Подтвердить заказ</button>
</form>
<aside class="order-summary"><h2>Ваш заказ</h2>
@foreach($rows as $row)<div class="order-line"><span>{{$row['product']->name}} × {{$row['quantity']}}</span><span>{{number_format((float)$row['product']->price*$row['quantity'],2,',',' ')}} ₽</span></div>@endforeach
<p id="order-total">Итого: {{number_format($rows->sum(fn($r)=>(float)$r['product']->price*$r['quantity']),2,',',' ')}} ₽</p>
<p>После оформления сохраните закрытую ссылку на заказ. Письмо автоматически не отправляется.</p><a href="{{route('cart')}}">Изменить корзину</a></aside>
</div>
@endsection
