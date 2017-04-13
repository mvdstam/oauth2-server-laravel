<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateOauthAccessTokenScopeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth2_access_token_scope', function(Blueprint $table) {
            $table->char('access_token_id', 80);
            $table->string('scope_id');

            $table->foreign('access_token_id')
                ->references('id')->on('oauth2_access_tokens')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('scope_id')
                ->references('id')->on('oauth2_scopes')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('oauth2_access_token_scope');
    }
}
