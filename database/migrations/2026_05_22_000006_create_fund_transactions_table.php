<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fund_transactions')) {
            return;
        }

        Schema::create('fund_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_fund_source')
                ->constrained('fund_sources')
                ->cascadeOnDelete();
            $table->enum('direction', ['in', 'out']);
            $table->decimal('amount', 14, 2);
            $table->date('transaction_date');
            $table->string('description', 500)->nullable();
            $table->string('attachment_path', 255)->nullable();
            // Reference polymorphic ke entitas sumber (manual / student_payment / teacher_honor).
            $table->string('reference_type', 40)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['id_fund_source', 'transaction_date'], 'fund_tx_source_date_idx');
            $table->index(['reference_type', 'reference_id'], 'fund_tx_reference_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fund_transactions');
    }
};
