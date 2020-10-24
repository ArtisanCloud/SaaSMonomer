<?php

namespace ArtisanCloud\SaaSMonomer\Http\Middleware;

use App\Services\UserService\UserService;
use ArtisanCloud\SaaSFramework\Http\Controllers\API\APIResponse;


use ArtisanCloud\SaaSMonomer\Services\LandlordService\src\LandlordService;
use ArtisanCloud\SaaSPolymer\Services\ArtisanService\src\ArtisanService;
use ArtisanCloud\SaaSPolymer\Services\ArtisanService\src\Models\Artisan;
use Closure;

class CheckUser
{
    protected APIResponse $apiResponse;
    protected UserService $userService;

    public function __construct(UserService $userService, APIResponse $APIResponse)
    {
        $this->userService = $userService;
        $this->apiResponse = $APIResponse;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        // set auth artisan into session
        $artisan = \Auth::user();
//        dd($artisan);
        ArtisanService::setAuthArtisan($artisan);

        // get session landord
        $sessionLandlord = LandlordService::getSessionLandlord();
//        dd($sessionLandlord);

        // load auth user into session
        $user = $this->userService->loadUserByLandlord($artisan, $sessionLandlord);
//        dd($user,$this->userService);
        if(!is_null($user) && !$user->isValid()){
            $this->setCode(API_ERR_CODE_INVALID_LOGIN_USER);
        }else{
            UserService::setAuthUser($user);
        }

        if(!$this->apiResponse->isNoError()){
            // we can log here and check where access our server with invalid request
            return $apiResponse->toJson();
        }

        return $next($request);
    }
}
