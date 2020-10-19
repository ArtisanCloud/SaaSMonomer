<?php

use ArtisanCloud\SaaSFramework\Services\TenantService\database\migrations\TenantMigration;
use Illuminate\Database\Schema\Blueprint;

class CreateRArtisanToTeamTable extends TenantMigration
{
    protected $table = 'r_artisan_to_team';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!$this->schema->hasTable($this->table)) {
            $this->schema->create($this->table, function (Blueprint $table) {
//                $table->bigIncrements('id');

				$table->string('artisan_uuid')->index();
                $table->string('team_uuid')->index();

            });
        }
    }
}
