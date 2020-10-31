<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Services\OrgService\Http\API;

use ArtisanCloud\SaaSMonomer\Services\OrgService\Http\Requests\RequestOrgCreate;
use ArtisanCloud\SaaSMonomer\Services\OrgService\Http\Requests\RequestOrgReadItem;
use ArtisanCloud\SaaSMonomer\Services\OrgService\Http\Requests\RequestOrgReadItems;
use ArtisanCloud\SaaSMonomer\Services\OrgService\Http\Resources\OrgResource;
use App\Models\Tenants\Org;


use App\Services\UserService\UserService;
use ArtisanCloud\SaaSFramework\Exceptions\BaseException;
use ArtisanCloud\SaaSFramework\Http\Controllers\API\APIController;

use ArtisanCloud\SaaSFramework\Http\Controllers\API\APIResponse;
use ArtisanCloud\SaaSMonomer\Services\OrgService\OrgService;
use Illuminate\Http\Request;


class OrgAPIController extends APIController
{
    private $orgService;

    function __construct(Request $request, OrgService $orgService)
    {
        // init the default value
        // parent will construction automatically
        parent::__construct($request);

        $this->orgService = $orgService;
    }


    public function apiCreate(RequestOrgCreate $request)
    {
        $org = \DB::connection('pgsql')->transaction(function () use ($request) {

            try {
                $arrayData = $request->all();
//                dd($arrayData);

                $org = $this->orgService->createBy($arrayData);
//                dd($org);
                if (is_null($org)) {
                    throw new \Exception('', API_ERR_CODE_FAIL_TO_CREATE_ORG);
                }

            } catch (\Exception $e) {
//                dd($e);
                throw new BaseException(
                    intval($e->getCode()),
                    $e->getMessage()
                );
            }

            return $org;

        });

        $this->m_apiResponse->setData(new OrgResource($org));

        return $this->m_apiResponse->toResponse();
    }

    public function apiReadItem(RequestOrgReadItem $request)
    {
        $org = $request->input('org');
        $org->load('users');

        $this->m_apiResponse->setData(new OrgResource($org));

        return $this->m_apiResponse->toResponse();

    }

    public function apiReadList(RequestOrgReadItems $request)
    {
        $user = UserService::getAuthUser();

        $orgs = $this->orgService->getListBy($user);
//        dd($orgs);

        $this->m_apiResponse->setData(OrgResource::collection($orgs));

        return $this->m_apiResponse->toResponse();

    }


}
