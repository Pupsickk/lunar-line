@extends('layouts.app')
@section('title','Вход продавца — Лунная линия')
@section('content')<h1>Вход продавца</h1><form class="server-form" method="post" action="/login">@csrf<label for="email">Email</label><input id="email" name="email" type="email" required value="{{old('email')}}"><label for="password">Пароль</label><input id="password" name="password" type="password" required autocomplete="current-password"><button class="btn">Войти</button></form>@endsection
