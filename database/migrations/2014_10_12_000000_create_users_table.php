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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name')->nullable();
            $table->string('first_name',200)->nullable();
            $table->string('last_name',200)->nullable();
            $table->string('init_name',200)->nullable();
            $table->string('email',100)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('nic',50)->unique()->nullable();
            $table->string('password')->nullable();
            $table->char('role',1)->default('U')->comment('U : User , A : Admin');
            $table->char('designation',1)->nullable();
            $table->string('mobile',20)->nullable();
            $table->string('phone',20)->nullable();
            $table->integer('department')->nullable();
            $table->string('emp_no',100)->nullable();
            $table->text('address_1')->nullable();
            $table->text('address_2')->nullable();
            $table->text('image')->nullable();
            $table->text('district')->nullable();
            $table->char('status',1)->default('A')->nullable();
            $table->rememberToken();
            $table->dateTime('created_at');
            $table->integer('created_by')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
// $2y$12$5YZu.s0o4lbatF3QxfcChudKvppp/hYdaWMzIb/qoz.u6rdVbgeRq
