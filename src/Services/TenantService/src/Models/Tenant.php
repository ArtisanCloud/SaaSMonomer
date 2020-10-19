<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Services\TenantService\src\Models;

use App\Models\User;
use App\Services\UserService\UserService;
use ArtisanCloud\SaaSFramework\Models\ArtisanCloudModel;
use ArtisanCloud\Commentable\Traits\Commentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;


class Tenant extends ArtisanCloudModel
{
    use Commentable;
    
    protected $connection = 'tenant';
    const TABLE_NAME = '';
    protected $table = self::TABLE_NAME;

    const IN_STATUS_UNDER_CONSTRUCTION = 1;
    const IN_STATUS_IN_DESIGN = 2;
    const IN_STATUS_READY_TO_DEVELOP = 3;
    const IN_STATUS_IN_DEVELOPMENT = 4;
    const IN_STATUS_READY_TO_SHIP = 5;
    const IN_STATUS_SHIPPED = 6;
    const IN_STATUS_WILL_NOT_IMPLEMENT = 7;

    const ARRAY_IN_STATUS = [
        self::IN_STATUS_UNDER_CONSTRUCTION => '构思中',
        self::IN_STATUS_IN_DESIGN => '设计中',
        self::IN_STATUS_READY_TO_DEVELOP => '准备开发',
        self::IN_STATUS_IN_DEVELOPMENT => '开发中',
        self::IN_STATUS_READY_TO_SHIP => '准备发布',
        self::IN_STATUS_SHIPPED => '已发布',
        self::IN_STATUS_WILL_NOT_IMPLEMENT => '未能实现',
    ];

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
