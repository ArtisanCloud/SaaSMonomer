<?php

namespace ArtisanCloud\SaaSMonomer\Http\Middleware;

use App\Services\UserService\UserService;
use ArtisanCloud\SaaSFramework\Http\Controllers\API\APIResponse;


use ArtisanCloud\SaaSMonomer\Services\OrgService\Models\Org;
use ArtisanCloud\SaaSMonomer\Services\OrgService\OrgService;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\TenantService;
use Closure;

class CheckTenant
{
    protected APIResponse $apiResponse;
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService, APIResponse $APIResponse)
    {
        $this->tenantService = $tenantService;
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
        $org = $this->loadSessionOrg($request);
//        dd($org);
        if (is_null($org)) {
            $this->apiResponse->setCode(API_ERR_CODE_ORG_NOT_EXIST);
        }else{
            $tenant = $org->tenant;
//            dd($tenant);
            if(is_null($tenant)){
                
                $this->apiResponse->setLocaleNamespace(TENANT_LANG);
                $this->apiResponse->setCode(API_ERR_CODE_TENANT_NOT_EXIST);
            }else{
                $this->tenantService->setConnection($tenant);
            }
        }

        if (!$this->apiResponse->isNoError()) {
            // we can log here and check where access our server with invalid request
            return $this->apiResponse->toJson();
        }
//        dd($org);

        return $next($request);
    }

    /**
     * Load current session Org.
     *
     * @param \Illuminate\Http\Request $request
     * @return Org
     */
    protected function loadSessionOrg($request): ?Org
    {
        // set auth artisan into session
        $headerOrgUuid = $request->header('orgUuid');

        // return session org with same
        /** later replace the session with cache */
        $sessionOrg = OrgService::getSessionOrg();
        if (!is_null($sessionOrg) && $sessionOrg->uuid == $headerOrgUuid) {
            return $sessionOrg;
        }

        $headerOrg = null;
        // check header org exists or not
        if($headerOrgUuid){
            $headerOrg = OrgService::GetBy([
                'uuid' => $headerOrgUuid,
            ]);
        }

        if (is_null($headerOrg)) {
            // get default org for current session
            $user = UserService::getAuthUser();
            $defautlOrg = $user->myOrgs()->first();

            $org = $defautlOrg;
        }else{
            $org = $headerOrg;
        }

        // set new org into session
        if($org){
            /** later replace the session with cache */
            OrgService::setSessionOrg($org);
        }

        return $org;
    }
}
