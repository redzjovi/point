<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryDeliveryNoteItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_delivery_note_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('history_delivery_note_id')->index();
            $table->unsignedInteger('item_id')->index();
            $table->string('item_name')->index();
            $table->decimal('quantity_remaining', 65, 30)->index();
            $table->decimal('quantity', 65, 30)->index();
            $table->string('unit')->index();
            $table->decimal('converter', 65, 30)->index();
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
        Schema::dropIfExists('history_delivery_note_items');
    }
}
