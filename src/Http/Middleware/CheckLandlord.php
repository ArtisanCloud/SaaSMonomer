<?php

namespace ArtisanCloud\SaaSMonomer\Http\Middleware;

use ArtisanCloud\SaaSFramework\Http\Controllers\API\APIResponse;
use App\Services\UserService;
use ArtisanCloud\SaaSMonomer\Services\LandlordService\src\LandlordService;
use Closure;

class CheckLandlord
{

    protected APIResponse $apiResponse;
    protected LandlordService $landlordService;

    public function __construct(LandlordService $landlordService, APIResponse $APIResponse)
    {
        $this->landlordService = $landlordService;
        $this->apiResponse = $APIResponse;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        $host = $request->server('HTTP_HOST');
        $host = $request->server('SERVER_NAME');
//        dd($host);

        $landlord = $this->landlordService->getCachedModelForClientByKey('domain', $host);
//        dd($landlord);

        if (!$landlord) {
            $this->apiResponse->setCode(API_ERR_CODE_LANDLORD_NOT_EXIST);
        }else{
            $this->landlordService->setSessionLandlord($landlord);
        }

        if (!$this->apiResponse->isNoError()) {
            // we can log here and check where access our server with invalid request
            return $this->apiResponse->toJson();
        }

        return $next($request);
    }
}
