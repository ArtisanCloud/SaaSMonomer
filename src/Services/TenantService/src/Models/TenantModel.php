<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Services\TenantService\src\Models;

use App\Services\UserService\UserService;
use ArtisanCloud\SaaSFramework\Models\ArtisanCloudModel;

use ArtisanCloud\Commentable\Traits\Commentable;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


class TenantModel extends ArtisanCloudModel
{
    use Commentable;
    use SoftDeletes;
    
    protected $connection = 'tenant';
    const TABLE_NAME = '';
    protected $table = self::TABLE_NAME;

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
     * Get creator.
     *
     * @return BelongsTo
     *
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
