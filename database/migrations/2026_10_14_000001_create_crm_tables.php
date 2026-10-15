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
        Schema::create('sales_agents', function (Blueprint $table) {
            $table->id();
            $table->string('employee_name', 150);
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable()->unique();
            $table->string('password');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamp('created_at')->nullable()->useCurrent();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_title');
            $table->string('customer_name', 150);
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('message')->nullable();
            $table->foreignId('assigned_agent_id')->nullable()->constrained('sales_agents')->nullOnDelete();
            $table->enum('status', ['Won', 'Lost', 'In Process', 'Not Responding'])->default('In Process');
            $table->boolean('is_new')->default(true);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('lead_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->enum('added_by', ['Admin', 'Agent'])->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('sales_agents')->nullOnDelete();
            $table->text('note');
            $table->timestamp('created_at')->nullable()->useCurrent();
        });

        Schema::create('lead_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->string('action')->nullable();
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->enum('performed_by', ['Admin', 'Agent'])->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('sales_agents')->nullOnDelete();
            $table->timestamp('created_at')->nullable()->useCurrent();
        });

        // One table for both admins and agents; role says which table the email belongs to.
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id();
            $table->enum('role', ['Admin', 'Agent']);
            $table->string('email', 150);
            $table->string('token', 64)->index();
            $table->dateTime('expires_at');
            $table->boolean('used')->default(false);
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('lead_activity_logs');
        Schema::dropIfExists('lead_notes');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('sales_agents');
    }
};
