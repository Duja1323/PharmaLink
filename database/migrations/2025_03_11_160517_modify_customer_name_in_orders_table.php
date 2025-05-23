<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('customer_phone')->default('')->change();  // تعيين قيمة افتراضية فارغة
        });
    }
    
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('customer_phone')->nullable()->change();  // إذا أردت التراجع، جعله nullable
        });
    }
};
