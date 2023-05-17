<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('title');
            $table->text('content');
            $table->float('hour_estimate');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('priority', ['Low', 'Medium', 'High', 'Urgent']);
            $table->unsignedBigInteger('leader_id_assigned');
            $table->integer('order_position')->nullable();
            $table->timestamps();
            $table->foreign('leader_id_assigned')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
};
