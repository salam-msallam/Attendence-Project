<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card-transaction',function(Blueprint $table){
            $table->id();
            $table->string('type')->default("enter");
            $table->foreignId('card_id')->constrained()->onDelete('cascade');
            $table->timestamps();

        });
    }

    public function down(): void
    {
        
    }
};
