<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateOAuthAuthCodeScopeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth2_auth_code_scope', function(Blueprint $table) {
            $table->char('auth_code_id', 80);
            $table->string('scope_id');

            $table->foreign('auth_code_id')
                ->references('id')->on('oauth2_auth_codes')
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
        Schema::drop('oauth2_auth_code_scope');
    }
}
