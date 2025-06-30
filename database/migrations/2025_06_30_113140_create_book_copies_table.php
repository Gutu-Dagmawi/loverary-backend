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
        Schema::create('book_copies', function (Blueprint $table) {
            $table->string('barcode', 100)->primary();
            $table->unsignedBigInteger('book_id');
            $table->string('condition')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_available')->default(true);

            $table->foreign('book_id')->references('book_id')->on('books')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_copies');
    }
};
