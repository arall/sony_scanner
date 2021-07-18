<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostWebsiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('host_website', function (Blueprint $table) {
            $table->foreignId('host_id')->nullable()->constrained()->onDelete('CASCADE');
            $table->foreignId('website_id')->nullable()->constrained()->onDelete('CASCADE');
            $table->foreignId('port_id')->nullable()->constrained()->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('host_website');
    }
}
