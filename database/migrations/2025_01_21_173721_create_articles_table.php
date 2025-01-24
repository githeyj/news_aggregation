<?php

use App\Models\User;
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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('slug')->unique();
            $table->text('excerpt');
            $table->text('content');
            $table->string('author')->nullable();
            $table->string('service')->comment('The service that provided the article');
            $table->string('source')->nullable();
            $table->string('source_url')->nullable();

            $table->foreignIdFor(User::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->text('image_url')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            // Indexes for better query performance
            $table->index('source');
            $table->index('published_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
