<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Model::unguard();

        DB::beginTransaction();

        try {
            DB::table('translations')->updateOrInsert(
                    [
                        'namespace' => 'user',
                        'group'     => 'users',
                        'key'       => 'unauthenticated'
                    ],
                    [
                        'text'       => json_encode(['en' => 'Unauthenticated', 'ko' => '인증되지 않음.'], JSON_UNESCAPED_UNICODE),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            DB::commit();
        } catch (\Exception $e) {
            echo 'failed to seed';
            DB::rollBack();
        }

    }
};
