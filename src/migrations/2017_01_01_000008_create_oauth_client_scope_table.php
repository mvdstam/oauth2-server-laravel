<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateOAuthClientScopeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth2_client_scope', function(Blueprint $table) {
            $table->char('client_id', 36);
            $table->string('scope_id');

            $table->foreign('client_id')
                ->references('id')->on('oauth2_clients')
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
        Schema::drop('oauth2_client_scope');
    }
}
