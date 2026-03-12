<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('assign_customer_name')->nullable()->after('service_address_id');
            $table->string('assign_customer_phone')->nullable()->after('assign_customer_name');
            $table->string('assign_customer_email')->nullable()->after('assign_customer_phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'assign_customer_name',
                'assign_customer_phone',
                'assign_customer_email'
            ]);
        });
    }
}
