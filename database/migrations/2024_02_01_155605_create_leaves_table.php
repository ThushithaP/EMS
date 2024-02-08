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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->integer('staff_id')->nullable();
            $table->string('staff_name',100)->nullable();
            $table->string('staff_email',100)->nullable();
            $table->string('staff_mobile',20)->nullable();
            $table->string('staff_emp',50)->nullable();
            $table->char('leave_type',1)->nullable();
            $table->date('leave_from')->nullable();
            $table->date('report_date')->nullable();
            $table->string('days',20)->nullable();
            $table->string('supervisor',100)->nullable();
            $table->date('leave_to')->nullable();
            $table->string('res_person',100)->nullable();
            $table->text('reason')->nullable();
            $table->char('approve_status',1)->default('P')->nullable();
            $table->integer('approved_by')->nullable();
            $table->dateTime('created_at')->nullable();
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
        Schema::dropIfExists('leaves');
    }
};
