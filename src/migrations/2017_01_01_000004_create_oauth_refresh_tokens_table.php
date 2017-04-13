<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateOauthRefreshTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth2_refresh_tokens', function(Blueprint $table) {
            $table->char('id', 80)->primary();
            $table->char('access_token_id', 80);

            $table->foreign('access_token_id')
                ->references('id')->on('oauth2_access_tokens')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->dateTime('expires_at');
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
        Schema::drop('oauth2_refresh_tokens');
    }
}
