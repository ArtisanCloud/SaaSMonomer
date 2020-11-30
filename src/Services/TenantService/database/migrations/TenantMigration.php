<?php

namespace ArtisanCloud\SaaSMonomer\Services\TenantService\database\migrations;

use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Models\TenantModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantMigration extends Migration
{
    protected $schema = null;

    protected $table = '';

    function __construct()
    {
        $this->schema = Schema::connection(TenantModel::getConnectionNameStatic())->getConnection()->getSchemaBuilder();
//        dd($this->schema);

    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        dump($this->table);
        $this->schema->dropIfExists($this->table);
//        if (app()->environment('local')) {
//            $this->schema->dropIfExists($this->table);
//        }
    }
}
