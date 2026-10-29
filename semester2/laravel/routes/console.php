<?php
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
Artisan::command('app:create-admin {email} {--name=Администратор}',function(){ $email=$this->argument('email');if(!filter_var($email,FILTER_VALIDATE_EMAIL)){return $this->error('Неверный email');}if(User::where('email',$email)->exists()){return $this->error('Пользователь уже существует. Учётная запись не изменена.');}$password=$this->secret('Пароль администратора (минимум 12 символов)');if(strlen((string)$password)<12)return $this->error('Пароль слишком короткий');$u=new User();$u->name=$this->option('name');$u->email=$email;$u->password=Hash::make($password);$u->role='admin';$u->save();$this->info('Администратор создан');});
