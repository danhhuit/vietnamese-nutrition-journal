<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('allergy_tags', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('health_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->date('birth_date')->nullable();
            $table->string('gender', 20)->nullable();
            $table->decimal('height_cm', 6, 2)->nullable();
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->string('activity_level', 30)->default('moderate');
            $table->string('goal', 30)->default('maintain');
            $table->decimal('target_weight_kg', 6, 2)->nullable();
            $table->date('target_date')->nullable();
            $table->decimal('daily_calorie_target', 8, 2)->nullable();
            $table->decimal('protein_target_g', 8, 2)->nullable();
            $table->decimal('fat_target_g', 8, 2)->nullable();
            $table->decimal('carb_target_g', 8, 2)->nullable();
            $table->unsignedInteger('water_target_ml')->default(2000);
            $table->json('dietary_preferences')->nullable();
            $table->json('reminders')->nullable();
            $table->timestamps();
        });

        Schema::create('allergy_tag_health_profile', function (Blueprint $table): void {
            $table->foreignId('allergy_tag_id')->constrained()->cascadeOnDelete();
            $table->foreignId('health_profile_id')->constrained()->cascadeOnDelete();
            $table->primary(['allergy_tag_id', 'health_profile_id'], 'allergy_profile_pk');
        });

        Schema::create('food_groups', function (Blueprint $table): void {
            $table->id();
            $table->string('name_vi')->unique();
            $table->string('name_en')->nullable();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('foods', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('food_group_id')->constrained()->restrictOnDelete();
            $table->string('name')->unique();
            $table->string('normalized_name')->index();
            $table->string('origin', 30)->default('plant')->index();
            $table->string('source')->nullable();
            $table->decimal('calories', 8, 2)->default(0);
            $table->decimal('protein_g', 8, 2)->default(0);
            $table->decimal('fat_g', 8, 2)->default(0);
            $table->decimal('carb_g', 8, 2)->default(0);
            $table->decimal('fiber_g', 8, 2)->default(0);
            $table->decimal('calcium_mg', 8, 2)->default(0);
            $table->decimal('iron_mg', 8, 2)->default(0);
            $table->decimal('sodium_mg', 8, 2)->default(0);
            $table->decimal('potassium_mg', 8, 2)->default(0);
            $table->decimal('vitamin_a_mcg', 8, 2)->default(0);
            $table->decimal('vitamin_c_mg', 8, 2)->default(0);
            $table->unsignedInteger('viewed_count')->default(0);
            $table->timestamps();
            $table->index(['food_group_id', 'calories']);
        });

        Schema::create('serving_units', function (Blueprint $table): void {
            $table->id();
            $table->string('name_vi')->unique();
            $table->string('name_en')->nullable();
            $table->decimal('grams', 8, 2);
            $table->string('applies_to', 20)->default('both');
            $table->timestamps();
        });

        Schema::create('dishes', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('normalized_name')->index();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->decimal('serving_grams', 8, 2)->default(100);
            $table->decimal('calories', 8, 2)->default(0)->index();
            $table->decimal('protein_g', 8, 2)->default(0);
            $table->decimal('fat_g', 8, 2)->default(0);
            $table->decimal('carb_g', 8, 2)->default(0);
            $table->decimal('fiber_g', 8, 2)->default(0);
            $table->decimal('sodium_mg', 8, 2)->default(0);
            $table->decimal('sugar_g', 8, 2)->default(0);
            $table->boolean('is_low_calorie')->default(false)->index();
            $table->boolean('is_low_sodium')->default(false)->index();
            $table->boolean('is_high_protein')->default(false)->index();
            $table->boolean('is_vegetarian')->default(false)->index();
            $table->string('data_source')->nullable();
            $table->unsignedInteger('viewed_count')->default(0);
            $table->timestamps();
        });

        Schema::create('allergy_tag_dish', function (Blueprint $table): void {
            $table->foreignId('allergy_tag_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dish_id')->constrained()->cascadeOnDelete();
            $table->primary(['allergy_tag_id', 'dish_id']);
        });

        Schema::create('dish_food', function (Blueprint $table): void {
            $table->foreignId('dish_id')->constrained()->cascadeOnDelete();
            $table->foreignId('food_id')->constrained('foods')->restrictOnDelete();
            $table->decimal('grams', 8, 2);
            $table->primary(['dish_id', 'food_id']);
        });

        Schema::create('restaurants', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('normalized_name')->index();
            $table->string('address');
            $table->string('area')->index();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('opening_hours')->nullable();
            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('price_level', 20)->default('medium')->index();
            $table->string('image_url')->nullable();
            $table->date('data_updated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('dish_restaurant', function (Blueprint $table): void {
            $table->foreignId('dish_id')->constrained()->cascadeOnDelete();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('price');
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->primary(['dish_id', 'restaurant_id']);
        });

        Schema::create('daily_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('logged_on');
            $table->timestamps();
            $table->unique(['user_id', 'logged_on']);
        });

        Schema::create('food_log_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('daily_log_id')->constrained()->cascadeOnDelete();
            $table->foreignId('food_id')->nullable()->constrained('foods')->nullOnDelete();
            $table->foreignId('dish_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('restaurant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('meal_type', 20)->index();
            $table->string('item_name')->nullable();
            $table->decimal('quantity', 7, 2)->default(1);
            $table->decimal('serving_grams', 8, 2);
            $table->decimal('calories', 8, 2)->default(0);
            $table->decimal('protein_g', 8, 2)->default(0);
            $table->decimal('fat_g', 8, 2)->default(0);
            $table->decimal('carb_g', 8, 2)->default(0);
            $table->decimal('sodium_mg', 8, 2)->default(0);
            $table->decimal('sugar_g', 8, 2)->default(0);
            $table->string('source', 20)->default('manual');
            $table->timestamps();
        });

        Schema::create('water_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('logged_on')->index();
            $table->unsignedInteger('amount_ml');
            $table->timestamps();
        });

        Schema::create('weight_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('recorded_on')->index();
            $table->decimal('weight_kg', 6, 2);
            $table->timestamps();
            $table->unique(['user_id', 'recorded_on']);
        });

        Schema::create('favorites', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('favoritable_type');
            $table->unsignedBigInteger('favoritable_id');
            $table->timestamps();
            $table->unique(['user_id', 'favoritable_type', 'favoritable_id'], 'favorite_unique');
            $table->index(['favoritable_type', 'favoritable_id']);
        });

        Schema::create('settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('type', 20)->default('number');
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('search_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('query');
            $table->unsignedInteger('results_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_logs');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('weight_entries');
        Schema::dropIfExists('water_logs');
        Schema::dropIfExists('food_log_entries');
        Schema::dropIfExists('daily_logs');
        Schema::dropIfExists('dish_restaurant');
        Schema::dropIfExists('restaurants');
        Schema::dropIfExists('dish_food');
        Schema::dropIfExists('allergy_tag_dish');
        Schema::dropIfExists('dishes');
        Schema::dropIfExists('serving_units');
        Schema::dropIfExists('foods');
        Schema::dropIfExists('food_groups');
        Schema::dropIfExists('allergy_tag_health_profile');
        Schema::dropIfExists('health_profiles');
        Schema::dropIfExists('allergy_tags');
    }
};
