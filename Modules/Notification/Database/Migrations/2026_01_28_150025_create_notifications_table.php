<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('notifications', function (Blueprint $table) {
             $table->uuid('id')->primary();
            $table->string('user_id');
            $table->string('user_type')->default('user');
            $table->string('title');
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->string('module')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
