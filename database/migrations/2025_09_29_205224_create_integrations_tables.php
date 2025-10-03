<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('integrations_requests', function (Blueprint $t) {
            $t->string('idem_key', 64)->primary();
            $t->json('response_json');
            $t->timestamp('created_at')->index();
        });

        Schema::create('integrations_logs', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('request_id', 64)->nullable()->index();
            $t->unsignedBigInteger('usuario_id')->nullable()->index();
            $t->string('intent', 64)->nullable()->index();
            $t->string('status', 16)->default('start')->index(); // start|ok|error
            $t->string('message', 255)->nullable();
            $t->timestamp('started_at')->nullable()->index();
            $t->timestamp('ended_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integrations_logs');
        Schema::dropIfExists('integrations_requests');
    }
};
