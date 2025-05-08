<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->onDelete('cascade');
            $table->string('language')->default('es_CL');
            $table->string('date_format')->default('dd/mm/yyyy');
            $table->string('theme')->default('light');
            $table->string('voice_preference')->default('female_1');
            $table->string('default_view')->default('dashboard');
            $table->boolean('voice_match')->default(false);
            $table->boolean('calendar_email')->default(false);
            $table->boolean('calendar_push')->default(false);
            $table->integer('reminder_time')->default(15);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('preferences');
    }
};