<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['umkm', 'buyer'])->default('umkm');
            $table->string('phone')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('assessments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->jsonb('answers');
            $table->string('product_category');
            $table->string('target_country');
            $table->unsignedSmallInteger('score')->default(0);
            $table->string('level')->default('Belum Siap');
            $table->jsonb('ai_prediction')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('category');
            $table->text('description');
            $table->decimal('price_usd', 10, 2);
            $table->unsignedInteger('production_capacity');
            $table->jsonb('target_countries')->default('[]');
            $table->jsonb('certifications')->default('[]');
            $table->jsonb('images')->default('[]');
            $table->enum('status', ['published', 'draft'])->default('published');
            $table->timestamps();

            $table->index('category');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('users');
    }
};
