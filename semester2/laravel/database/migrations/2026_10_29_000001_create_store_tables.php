<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('users',function(Blueprint $t){$t->id();$t->string('name');$t->string('email')->unique();$t->timestamp('email_verified_at')->nullable();$t->string('password');$t->string('role')->default('user');$t->rememberToken();$t->timestamps();});
  Schema::create('profiles',function(Blueprint $t){$t->id();$t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();$t->string('phone')->nullable();$t->timestamps();});
  Schema::create('categories',function(Blueprint $t){$t->id();$t->string('name');$t->string('slug')->unique();$t->text('description')->nullable();$t->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();$t->integer('sort_order')->default(0);$t->timestamps();});
  Schema::create('products',function(Blueprint $t){$t->id();$t->foreignId('category_id')->constrained()->restrictOnDelete();$t->string('name');$t->string('slug')->unique();$t->text('description');$t->decimal('price',10,2);$t->unsignedInteger('quantity')->default(100);$t->string('image');$t->unsignedSmallInteger('cards')->default(3);$t->unsignedSmallInteger('duration')->default(15);$t->boolean('is_new')->default(false);$t->boolean('is_hit')->default(false);$t->boolean('status')->default(true);$t->timestamps();$t->index(['status','category_id']);$t->index('price');});
  Schema::create('orders',function(Blueprint $t){$t->id();$t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();$t->uuid('order_number')->unique();$t->string('access_token_hash',64);$t->uuid('checkout_key')->unique();$t->string('customer_name');$t->string('email');$t->string('phone')->nullable();$t->text('notes')->nullable();$t->decimal('total_amount',12,2);$t->string('status')->default('new')->index();$t->string('payment_method')->default('not_connected');$t->timestamps();});
  Schema::create('order_product',function(Blueprint $t){$t->id();$t->foreignId('order_id')->constrained()->cascadeOnDelete();$t->foreignId('product_id')->constrained()->restrictOnDelete();$t->string('product_name');$t->unsignedInteger('quantity');$t->decimal('price',10,2);$t->timestamps();$t->unique(['order_id','product_id']);});
  Schema::create('reviews',function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained()->cascadeOnDelete();$t->foreignId('product_id')->constrained()->cascadeOnDelete();$t->unsignedTinyInteger('rating');$t->text('comment');$t->boolean('status')->default(false);$t->timestamps();});
 }
 public function down(): void {foreach(['reviews','order_product','orders','products','categories','profiles','users'] as $table)Schema::dropIfExists($table);}
};
