<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subcategory_id')->constrained()->cascadeOnDelete();

            $table->string('number')->nullable();
            $table->string('size')->nullable();
            $table->string('hole_size')->nullable();

            $table->decimal('gross_weight',10,3)->nullable();
            $table->decimal('less_weight',10,3)->nullable();
            $table->decimal('weight',10,3)->nullable();

            $table->integer('quantity')->default(0);

            // multiple images stored as JSON
            $table->json('gallery')->nullable();

            $table->string('label_product')->nullable();
            $table->string('color')->nullable();
            $table->decimal('charge',10,2)->nullable();
            $table->string('bg_color')->nullable();
            $table->boolean('order_confirm')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

