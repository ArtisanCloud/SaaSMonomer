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
        /**
         * 这里有两个场景：
         * 一是，是在初始化的时候，使用root tenant来初始化项目
         * 二是，如果是每次给用户开tenant时，需要用"tenant" connection来migrate用户的基础表
         */
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
