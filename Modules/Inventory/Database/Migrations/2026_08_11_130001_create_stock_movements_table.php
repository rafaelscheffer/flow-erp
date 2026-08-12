<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stock movements are an immutable ledger: rows are only ever created,
     * never updated or deleted (corrections happen via a new "ajuste"
     * movement) — hence no softDeletes() here, unlike other module tables.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table): void {
            $table->id();
            $table->string('type');
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->restrictOnDelete();
            $table->foreignId('stock_location_id')->constrained('stock_locations')->restrictOnDelete();
            $table->integer('quantity');
            $table->uuid('transfer_group_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('performed_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index('transfer_group_id');
            $table->index(['product_id', 'product_variant_id', 'stock_location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
