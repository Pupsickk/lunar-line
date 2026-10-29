@extends('layouts.app')
@section('title','Заказ — Лунная линия')
@section('content')<h1>Заказ сохранён</h1><p>Номер: <strong>{{$order->order_number}}</strong></p><p>Статус: {{$order->status}}</p><p>{{$order->customer_name}}, {{$order->email}}</p>@foreach($order->products as $p)<p>{{$p->pivot->product_name}} × {{$p->pivot->quantity}} · {{$p->pivot->price}} ₽</p>@endforeach<p>Итого: {{$order->total_amount}} ₽</p><p>Оплата не подключена. Сохраните эту закрытую ссылку: она даёт доступ к деталям заказа.</p>@endsection
