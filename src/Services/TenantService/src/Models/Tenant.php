<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Services\TenantService\src\Models;

use App\Services\UserService\UserService;
use ArtisanCloud\SaaSFramework\Models\ArtisanCloudModel;

use ArtisanCloud\Commentable\Traits\Commentable;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


class Tenant extends TenantModel
{
    const TABLE_NAME = 'tenants';
    protected $table = self::TABLE_NAME;

    const TYPE_USER = 1;
    const TYPE_ORG = 2;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $user = UserService::getAuthUser();
            $model->created_by = $user ? $user->uuid : CREATED_BY_SYSTEM ;
//            dd($model);
        });
    }


    
    /**--------------------------------------------------------------- relation functions  -------------------------------------------------------------*/
    /**
     * Get user.
     *
     * @return BelongsTo
     *
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
