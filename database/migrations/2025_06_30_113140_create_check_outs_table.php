<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('check_outs', function (Blueprint $table) {
            $table->id('check_out_id');
            $table->unsignedBigInteger('member_id');
            $table->dateTime('check_out_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('due_date');
            $table->string('book_copy_barcode', 100);
            $table->timestamps();

            $table->foreign('member_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('book_copy_barcode')->references('barcode')->on('book_copies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_outs');
    }
};
