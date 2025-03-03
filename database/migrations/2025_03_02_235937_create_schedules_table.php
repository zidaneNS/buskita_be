<?php

use App\Models\Schedule;
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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->datetime('bus_schedule');
            $table->string('number');
            $table->bigInteger('capacity');
            $table->bigInteger('ikut_berangkat');
            $table->bigInteger('ikut_pulang');
            $table->bigInteger('berangkat');
            $table->bigInteger('pulang');
            $table->bigInteger('pp');
            $table->bigInteger('pp_khusus');
            $table->timestamps();
        });

        Schema::create('schedule_user', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Schedule::class)->constrained()->cascadeOnDelete();
            $table->enum('type', ['ikut_berangkat', 'ikut_pulang', 'berangkat', 'pulang', 'pp', 'pp_khusus']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('schedule_user');
    }
};
