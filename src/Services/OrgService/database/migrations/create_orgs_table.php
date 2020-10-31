<?php
declare(strict_types=1);

use ArtisanCloud\SaaSFramework\Models\ArtisanCloudModel;
use ArtisanCloud\SaaSMonomer\Services\OrgService\Models\Org;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orgs', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            
            $table->string('name');

            $table->tinyInteger('status')->default(Org::STATUS_INIT);
            $table->string('payment_status')->default(Org::STATUS_INIT);

            $table->timestamps();
        });

        Schema::create('org_profiles', function (Blueprint $table) {

            $table->uuid('uuid')->primary();
            $table->string('org_uuid')->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orgs');
    }
}