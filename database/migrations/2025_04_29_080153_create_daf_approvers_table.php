<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daf_approvers', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('request_id');
            $table->integer('approver_id');
            $table->integer('approver_status')->default('0');
            $table->integer('sequence');
	        $table->string('type');
            $table->integer('approved_by')->nullable();
            $table->timestamp('approved_date')->nullable();
            $table->integer('status')->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daf_approvers');
    }
};
