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
        Schema::table('messages', function (Blueprint $table) {
            $table->string('role', 50)->nullable(false)->change();
            $table->renameColumn('message', 'content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->enum('role', ['USER', 'CHATBOT'])->nullable(false)->change();
            $table->renameColumn('content', 'message');
        });
    }
};
