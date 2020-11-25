<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Services\OrgService\database\factories;

use ArtisanCloud\SaaSMonomer\Services\OrgService\Models\Org;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class OrgFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Org::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'short_name' => $this->faker->randomLetter,
            'status' => Org::STATUS_NORMAL,
            'payment_status' => Org::PAYMENT_STATUS_PAID,
            'uuid' => Str::uuid(),
        ];
    }
}

