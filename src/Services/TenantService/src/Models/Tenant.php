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

    const TYPE_USER = 1;
    const TYPE_ORG = 2;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subdomain',
        'org_uuid',
        'host',
        'database',
        'schema',
        'account',
        'password',
        'uri',
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
