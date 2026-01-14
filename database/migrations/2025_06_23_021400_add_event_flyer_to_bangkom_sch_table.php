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
        Schema::table('bangkom_schd', function (Blueprint $table) {
            // dalam method up()
            $table->string('event_flyer')->nullable()->after('event_tema'); // Simpan path gambar, bisa null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bangkom_sch', function (Blueprint $table) {
            //
        });
    }
};
