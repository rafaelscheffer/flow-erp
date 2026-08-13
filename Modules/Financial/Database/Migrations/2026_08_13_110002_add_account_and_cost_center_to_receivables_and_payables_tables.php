<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receivables', function (Blueprint $table): void {
            $table->foreignId('account_id')->nullable()->after('order_id')->constrained('accounts')->nullOnDelete();
            $table->foreignId('cost_center_id')->nullable()->after('account_id')->constrained('cost_centers')->nullOnDelete();
        });

        Schema::table('payables', function (Blueprint $table): void {
            $table->foreignId('account_id')->nullable()->after('purchase_order_id')->constrained('accounts')->nullOnDelete();
            $table->foreignId('cost_center_id')->nullable()->after('account_id')->constrained('cost_centers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('receivables', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('account_id');
            $table->dropConstrainedForeignId('cost_center_id');
        });

        Schema::table('payables', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('account_id');
            $table->dropConstrainedForeignId('cost_center_id');
        });
    }
};
