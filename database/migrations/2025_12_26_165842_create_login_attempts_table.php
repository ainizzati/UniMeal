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
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('guard')->default('web'); // student, cafeteria, web
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->boolean('successful')->default(false);
            $table->timestamp('attempted_at');
            $table->timestamps();

            // Indexes for performance
            $table->index(['email', 'guard']);
            $table->index('ip_address');
            $table->index('attempted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};
