<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateOauthAccessTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth2_access_tokens', function(Blueprint $table) {
            $table->char('id', 80)->primary();
            $table->char('user_id', 36)->nullable();
            $table->char('client_id', 36);

            $table->foreign('user_id')
                ->references('id')->on('oauth2_users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('client_id')
                ->references('id')->on('oauth2_clients')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->string('redirect_uri')->nullable();
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
        Schema::drop('oauth2_access_tokens');
    }
}
