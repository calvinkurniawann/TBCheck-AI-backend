<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('jenis_kelamin')->nullable()->after('email');
            $table->integer('usia')->nullable()->after('jenis_kelamin');
            $table->float('tinggi_badan', 5, 1)->nullable()->after('usia');
            $table->float('berat_badan', 5, 1)->nullable()->after('tinggi_badan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['jenis_kelamin', 'usia', 'tinggi_badan', 'berat_badan']);
        });
    }
};
