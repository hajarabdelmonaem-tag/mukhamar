<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('fcm_token', '')->update(['fcm_token' => null]);

        $this->releaseDuplicateTokens();

        Schema::table('users', function (Blueprint $table) {
            $table->string('fcm_token', 500)->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('fcm_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['fcm_token']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->text('fcm_token')->nullable()->change();
        });
    }

    /**
     * Keep only the most recently updated user for every duplicated device token.
     *
     * A device token shared by several accounts makes each of them receive a
     * separate copy of every broadcast, so the losers are released and will
     * re-claim the token the next time they register or log in.
     */
    private function releaseDuplicateTokens(): void
    {
        $tokens = DB::table('users')
            ->whereNotNull('fcm_token')
            ->select('fcm_token')
            ->selectRaw('COUNT(*) as aggregate')
            ->groupBy('fcm_token')
            ->having('aggregate', '>', 1)
            ->pluck('fcm_token');

        foreach ($tokens as $token) {
            $owner = DB::table('users')
                ->where('fcm_token', $token)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->value('id');

            DB::table('users')
                ->where('fcm_token', $token)
                ->where('id', '!=', $owner)
                ->update(['fcm_token' => null, 'updated_at' => now()]);
        }
    }
};
