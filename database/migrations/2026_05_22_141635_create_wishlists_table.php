<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('wishlists', function (Blueprint $table) {
        $table->id();

        // PENTING: Gunakan foreignUuid, BUKAN foreignId
        $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignUuid('product_id')->constrained('products')->onDelete('cascade');

        $table->timestamps();

        // Opsional: Mencegah user menyimpan produk yang sama lebih dari sekali
        $table->unique(['user_id', 'product_id']);
    });
}

    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
