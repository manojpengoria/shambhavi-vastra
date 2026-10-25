<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('mobile', 30)->nullable();
            $table->foreignId('role_id')
                ->nullable()
                ->after('mobile')
                ->constrained('roles')
                ->nullOnDelete();
        });

        $modelHasRolesTable = config(
            'permission.table_names.model_has_roles',
            'model_has_roles'
        );
        $rolePivotKey = config(
            'permission.column_names.role_pivot_key',
            'role_id'
        );
        $modelMorphKey = config(
            'permission.column_names.model_morph_key',
            'model_id'
        );

        DB::table($modelHasRolesTable)
            ->select($modelMorphKey, $rolePivotKey)
            ->where('model_type', 'App\\Models\\Admin')
            ->orderBy($rolePivotKey)
            ->get()
            ->unique($modelMorphKey)
            ->each(function (object $assignment) use ($modelMorphKey, $rolePivotKey): void {
                DB::table('admins')
                    ->where('id', $assignment->{$modelMorphKey})
                    ->update(['role_id' => $assignment->{$rolePivotKey}]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->dropColumn('mobile');
        });
    }
};
