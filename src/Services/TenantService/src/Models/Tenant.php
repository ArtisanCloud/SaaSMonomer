<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Services\TenantService\src\Models;

use App\Services\UserService\UserService;
use ArtisanCloud\SaaSFramework\Models\ArtisanCloudModel;

use ArtisanCloud\Commentable\Traits\Commentable;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class Tenant extends ArtisanCloudModel
{
    use Commentable;
    
    protected $connection = 'tenant';
    const TABLE_NAME = '';
    protected $table = self::TABLE_NAME;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $user = UserService::getAuthUser();
            $model->created_by = $user ? $user->uuid : 'System Creator' ;
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
