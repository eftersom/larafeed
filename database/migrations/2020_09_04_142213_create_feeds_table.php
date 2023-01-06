<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class  CreateFeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feeds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('slug');
            $table->string('link');
            $table->text('description')->nullable();
            $table->string('language')->nullable();
            $table->string('image', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['link', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('feeds', function (Blueprint $table) {
            $table->dropUnique(['link', 'slug']);
        });
        Schema::dropIfExists('feeds');
    }
}
