<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;



return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('check_outs', function (Blueprint $table) {
            $table->unsignedBigInteger('check_in_id')->nullable()->after('id');
            $table->foreign('check_in_id')->references('check_in_id')->on('check_ins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('check_outs', function (Blueprint $table) {
            $table->dropColumn('check_in_id');
        });
    }
};


