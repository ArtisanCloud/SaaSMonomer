<?php

namespace ArtisanCloud\SaaSMonomer\Services\OrgService\Http\Resources;

use ArtisanCloud\SaaSFramework\Http\Resources\BasicResource;
use App\Http\Resources\UserResource;


class OrgResource extends BasicResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $arrayTransformedKeys = transformArrayKeysToCamel($this->resource->getAttributes());
//        dd($arrayTransformedKeys);

        $arrayTransformedKeys["users"] = UserResource::collection($this->whenLoaded('users'));

        return $arrayTransformedKeys;

    }
}
