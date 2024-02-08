<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('dep_name',100);
            $table->string('dep_email',100);
            $table->text('description');
            $table->char('dep_status',1)->default('O')->comment('O : Operrative , I : Inoperative , D : Deleted');
            $table->dateTime('created_at');
            $table->integer('created_by');
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
// $2y$12$5YZu.s0o4lbatF3QxfcChudKvppp/hYdaWMzIb/qoz.u6rdVbgeRq