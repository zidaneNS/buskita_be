<?php

use App\Models\Bus;
use App\Models\Route;
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
            $table->dateTime('bus_schedule');
            $table->foreignIdFor(Bus::class);
            $table->foreignIdFor(Route::class);
            $table->boolean('closed')->default(false);
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });

        Schema::create('schedule_user', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Schedule::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
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
