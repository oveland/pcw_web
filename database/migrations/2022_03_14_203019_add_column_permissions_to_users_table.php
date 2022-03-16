<?php

use App\Models\System\ViewPermission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnPermissionsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('permissions')->nullable();
        });

        DB::statement("UPDATE acceso SET menureporte = menureporte || '123456' WHERE menureporte like '%,'");
        $migratedPermissions = ViewPermission::where('migrated', true)->get()->pluck('id')->implode(',');
        DB::statement("UPDATE acceso SET menureporte = menureporte || ',$migratedPermissions' WHERE menureporte like '%43%'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }
}
