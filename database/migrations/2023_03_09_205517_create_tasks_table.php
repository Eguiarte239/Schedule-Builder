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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('title');
            $table->text('content');
            $table->float('hour_estimate');
            $table->date('start_time');
            $table->date('end_time');
            $table->enum('priority', ['Low', 'Medium', 'High', 'Urgent']);
            $table->unsignedBigInteger('phase_id');
            $table->unsignedBigInteger('assigned_to_task');
            $table->unsignedBigInteger('predecessor_task')->nullable();
            $table->boolean('is_finished')->default(false);
            $table->integer('order_position')->nullable();
            $table->timestamps();
            $table->foreign('phase_id')->references('id')->on('phases');
            $table->foreign('assigned_to_task')->references('id')->on('users');
            $table->foreign('predecessor_task')->references('id')->on('tasks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
