<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Services\LandlordService\src\Models;

use App\Models\Product;
use App\Models\User;

use ArtisanCloud\SaaSFramework\Models\ArtisanCloudModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Landlord extends ArtisanCloudModel
{
    // Disable Laravel's mass assignment protection
    protected $guarded = [];

    const TABLE_NAME = 'landlords';
    protected $table = self::TABLE_NAME;

    /**--------------------------------------------------------------- relation functions  -------------------------------------------------------------*/

    /**
     * Get the landlord's users.
     *
     * @return HasMany
     *
     */
    public function users()
    {
        return $this->hasMany(User::class, 'landlord_uuid');
    }

    /**
     * Get the landlord's products.
     *
     * @return HasMany
     *
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'product_uuid');
    }
}
