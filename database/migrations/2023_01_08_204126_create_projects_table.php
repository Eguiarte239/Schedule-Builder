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
            $table->date('start_time');
            $table->date('end_time');
            $table->enum('priority', ['Low', 'Medium', 'High', 'Urgent'])->nullable();
            $table->unsignedBigInteger('leader_id_assigned');
            $table->string('image')->nullable();
            $table->integer('order_position')->nullable();
            $table->timestamps();
            $table->foreign('leader_id_assigned')->references('id')->on('users')->onDelete('set null');
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
