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
        Schema::create('phases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('title');
            $table->text('content');
            $table->float('hour_estimate');
            $table->date('start_time');
            $table->date('end_time');
            $table->enum('priority', ['Low', 'Medium', 'High', 'Urgent'])->nullable();
            $table->unsignedBigInteger('assigned_to_project');
            $table->integer('order_position')->nullable();
            $table->timestamps();
            $table->foreign('assigned_to_project')->references('id')->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('phases');
    }
};
