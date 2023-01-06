<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feed_user', function (Blueprint $table) {
            $table->uuid('feed_id')->index();
            $table->foreign('feed_id')->references('id')->on('feeds')->onDelete('cascade');
            $table->uuid('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::table('feed_user', function (Blueprint $table) {
            $table->dropForeign(['feed_id']);
            $table->dropColumn('feed_id');
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
        Schema::dropIfExists('feed_user');
    }
}
