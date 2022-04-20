<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryDeliveryNoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_delivery_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('delivery_note_id')->index();
            $table->unsignedInteger('delivery_order_id')->index();
            $table->unsignedInteger('warehouse_id')->index();
            $table->string('driver')->index();
            $table->string('license_plate')->index();
            $table->text('notes')->nullable();
            $table->unsignedInteger('request_by')->index();
            $table->unsignedInteger('approval_by')->index();
            $table->string('activity_type')->index();
            $table->unsignedInteger('activity_by')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history_delivery_notes');
    }
}
