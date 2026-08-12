<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_reservations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->restrictOnDelete();
            $table->foreignId('stock_location_id')->constrained('stock_locations')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->string('status')->default('active');
            $table->foreignId('reserved_by')->constrained('users')->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id', 'product_variant_id', 'stock_location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_reservations');
    }
};
