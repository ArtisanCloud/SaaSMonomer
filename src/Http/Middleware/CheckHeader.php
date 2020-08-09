<?php

namespace ArtisanCloud\SaaSMonomer\Http\Middleware;

use App\Http\Controllers\API\APIResponse;
use App\Models\Account;
use App\Models\ClientProfile;

use Closure;
use Illuminate\Support\Arr;

class CheckHeader
{
    use APIResponse;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'api/wxpay/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        // init with error codes
        $this->resetCodes();

        // set  session language
        $locale = ClientProfile::getSessionLocale();
        \App::setLocale($locale);


        // get  session language
//        $timezone = ClientProfile::getSessionTimezone();
//        dd($timezone);


        // check header
        $platform = $request->header('platform');
        $channel = $request->header('channel');
        $uuid = $request->header('uuid');

//        $account = (new Account())->getCachedDetailForClientByID($channel);
//        dd($account);

        if (!$platform || !in_array($platform, ClientProfile::ARRAY_PLATFORM)) {
            $this->setCode(API_ERR_CODE_HEADER_PLATFORM);

//        } elseif (!$channel || is_null($account) || $account->type!=Account::TYPE_BUSINESS) {
        } elseif (!$channel || !in_array($channel, ClientProfile::ARRAY_CHANNEL)) {
            $this->setCode(API_ERR_CODE_HEADER_SOURCE);

        } elseif (!$uuid) {
            $this->setCode(API_ERR_CODE_HEADER_UUID);

        }

        if(!$this->isNoError()){
            // we can log here and check where access our server with invalid request

            return $this->getJSONResponse();
        }


        return $next($request);
    }
}
