<?php

use App\Models\Contact;
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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()->nullable();
            $table->string('name', 100);
            $table->string('email', 100)->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('address', 100)->nullable();
            $table->enum('contact_type', Contact::getContactTypes())
                    ->default(Contact::CONTACT_TYPE_CLIENT);
            $table->timestamps();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};