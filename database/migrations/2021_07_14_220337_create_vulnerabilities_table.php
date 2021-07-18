<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVulnerabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vulnerabilities', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('severity_id')->nullable()->constrained()->onDelete('SET NULL');
            $table->string('name');
            $table->string('cwe')->nullable();
            $table->text('description')->nullable();
            $table->text('attack_details')->nullable();
            $table->text('remediation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vulnerabilities');
    }
}
