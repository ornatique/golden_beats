<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->boolean('admin')->default(0)->after('id');

            $table->string('state')->nullable()->after('email');
            $table->string('city')->nullable()->after('state');
            $table->string('number')->nullable()->after('city');

            $table->string('image')->nullable()->after('number');

            $table->string('otp')->nullable()->after('image');
            $table->boolean('status')->default(0)->after('otp');

            $table->string('device_token')->nullable()->after('status');
            $table->string('device_key')->nullable()->after('device_token');
            $table->string('token')->nullable()->after('device_key');
            $table->string('device_type')->nullable()->after('token');

            $table->string('company_name')->nullable()->after('remember_token');

            $table->text('category_ids')->nullable()->after('company_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'admin',
                'state',
                'city',
                'number',
                'image',
                'otp',
                'status',
                'device_token',
                'device_key',
                'token',
                'device_type',
                'company_name',
                'category_ids',
            ]);
        });
    }
};

