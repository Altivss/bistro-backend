<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->integer('category_id');
            $table->integer('rating');
            $table->text('message')->nullable();
            $table->string('status')->default('New');
            $table->text('response')->nullable();
            $table->text('internal_notes')->nullable();
            $table->boolean('archived')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
