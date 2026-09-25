<?php

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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('app_name')->nullable();
            $table->string('title')->nullable();
            $table->string('primary_phone')->nullable();
            $table->string('secondary_phone')->nullable();
            $table->string('primary_email')->nullable();
            $table->string('secondary_email')->nullable();
            $table->string('office_time')->nullable();
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->string('banner_one')->nullable();
            $table->string('banner_one_link')->nullable();
            $table->boolean('banner_one_status')->default(true);
            $table->string('banner_two')->nullable();
            $table->string('banner_two_link')->nullable();
            $table->boolean('banner_two_status')->default(true);
            $table->string('page_heading_bg')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_keyword')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_image')->nullable();
            $table->text('google_map')->nullable();
            $table->string('favicon')->nullable();
            $table->string('logo')->nullable();
            $table->string('footer_logo')->nullable();
            $table->string('placeholder')->nullable();
            $table->string('facebook_page')->nullable();
            $table->string('facebook_group')->nullable();
            $table->string('youtube')->nullable();
            $table->string('twitter')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('google')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('instagram')->nullable();
            $table->string('pinterest')->nullable();
            $table->json('sms_api_url')->nullable();
            $table->json('sms_api_key')->nullable();
            $table->json('sms_api_id')->nullable();
            $table->boolean('bkash_status')->default(true);
            $table->boolean('nagad_status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
