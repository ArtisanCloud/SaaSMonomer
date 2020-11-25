<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Services\OrgService\Models;

use App\Models\User;
use ArtisanCloud\SaaSFramework\Models\ArtisanCloudModel;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Models\Tenant;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Models\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Org extends TenantModel
{
    const TABLE_NAME = 'orgs';
    protected $connection = 'pgsql';
    protected $table = self::TABLE_NAME;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'short_name',
        'user_uuid',
        'status',
        'payment_status',
    ];

    const PAYMENT_STATUS_UNPAID = 0;            // unpaid
    const PAYMENT_STATUS_PAID = 1;              // paid
    const PAYMENT_STATUS_EXPIRED = 2;           // expired

    // Disable Laravel's mass assignment protection
    protected $guarded = [];

    /**--------------------------------------------------------------- relation functions  -------------------------------------------------------------*/
    
    /**
     * Get the user's orgs.
     *
     * @return BelongsToMany
     *
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'r_user_to_org', 'org_uuid', 'user_uuid');
    }
}
