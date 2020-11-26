<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Services\TenantService\src\Models;

use App\Services\UserService\UserService;

use ArtisanCloud\Commentable\Traits\Commentable;

use App\Models\User;
use ArtisanCloud\SaaSFramework\Models\ArtisanCloudModel;

use ArtisanCloud\SaaSMonomer\Services\OrgService\Models\Org;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


class Tenant extends ArtisanCloudModel
{
    const TABLE_NAME = 'tenants';
    protected $table = self::TABLE_NAME;

    const STATUS_INIT = 0;                  // init
    const STATUS_NORMAL = 1;                // normal
    const STATUS_CREATED_DATABASE = 2;      // in creating database
    const STATUS_CREATED_ACCOUNT = 3;       // in creating account
    const STATUS_MIGRATED_DATABASE = 5;     // in creating account
    const STATUS_SEEDED_DEMO = 6;           // in seeding demo
    const STATUS_INVALID = 4;               // soft deleted


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subdomain',
        'org_uuid',
        'host',
        'port',
        'database',
        'schema',
        'account',
        'password',
        'url',
    ];


    /**--------------------------------------------------------------- relation functions  -------------------------------------------------------------*/

    /**
     * Get ort.
     *
     * @return BelongsTo
     *
     */
    public function org()
    {
        return $this->belongsTo(Org::class, 'org_uuid');
    }
}
