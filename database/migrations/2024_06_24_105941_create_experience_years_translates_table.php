<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\LanguagesController;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('experience_years_translates', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->timestamps();
        });
        $experienceyears = LanguagesController::getExperienceTranslations();
        foreach ($experienceyears as $nameEn => $nameAr) {
            DB::table('experience_years_translates')->insert([
                'name_ar' => $nameAr,
                'name_en' => $nameEn,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experience_years_translates');
    }
};
