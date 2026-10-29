<?php
namespace App\Http\Requests;
class CheckoutRequest extends \Illuminate\Foundation\Http\FormRequest {
 public function authorize():bool{return true;}
 protected function prepareForValidation():void{$this->merge(['name'=>trim((string)$this->name),'email'=>trim((string)$this->email)]);}
 public function rules():array{return ['name'=>'required|string|min:2|max:80','email'=>'required|email|max:160','phone'=>['nullable','string','regex:/^[+0-9() -]{10,20}$/'],'notes'=>'nullable|string|max:2000','agree'=>'accepted','checkout_key'=>'required|uuid'];}
}
