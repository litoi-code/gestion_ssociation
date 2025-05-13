<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('funds', function (Blueprint $table) {
            $table->decimal('total_interest', 12, 2)->default(0);
        });
    }

    public function down()
    {
        Schema::table('funds', function (Blueprint $table) {
            $table->dropColumn('total_interest');
        });
    }
};