<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('google_map');
            $table->unsignedInteger('country_id');
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('area_id')->nullable();
            $table->string('products_interest')->nullable();
            $table->string('company_level')->nullable();
            $table->integer('status')->nullable();
            $table->boolean('active')->default(true);
            $table->mediumText('note')->nullable();
            $table->string('created_by');


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
        Schema::dropIfExists('clients');
    }
}
