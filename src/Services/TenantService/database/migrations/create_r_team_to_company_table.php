<?php

use ArtisanCloud\SaaSFramework\Services\TenantService\database\migrations\TenantMigration;
use Illuminate\Database\Schema\Blueprint;

class CreateRTeamToCompanyTable extends TenantMigration
{
    protected $table = 'r_team_to_company';
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
