<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('important_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->date('date');
            $table->boolean('recurs_annually')->default(false);
            $table->text('notes')->nullable();
            $table->enum('type',['birthday','anniversary','flowering','event','other']);
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('important_dates');
    }
};