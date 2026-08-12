<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Materialized cache of the current on-hand/reserved quantity per
     * product(+variant)/location, kept in sync by StockMovementObserver and
     * StockReservationObserver as movements/reservations are created — never
     * written to directly. No unique DB constraint on the product/variant/
     * location triple: Postgres treats NULL product_variant_id as distinct
     * per row, so uniqueness for simple (non-variant) products is guaranteed
     * by the observers' find-or-create logic instead.
     */
    public function up(): void
    {
        Schema::create('stock_balances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->restrictOnDelete();
            $table->foreignId('stock_location_id')->constrained('stock_locations')->restrictOnDelete();
            $table->integer('quantity')->default(0);
            $table->unsignedInteger('reserved_quantity')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'product_variant_id', 'stock_location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_balances');
    }
};
