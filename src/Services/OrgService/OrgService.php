<?php

namespace ArtisanCloud\SaaSMonomer\Services\OrgService;

use App\Models\User;
use ArtisanCloud\SaaSMonomer\Services\OrgService\Contracts\OrgServiceContract;
use ArtisanCloud\SaaSFramework\Services\ArtisanCloudService;
use ArtisanCloud\SaaSMonomer\Services\OrgService\Jobs\CreateOrg;
use ArtisanCloud\SaaSMonomer\Services\OrgService\Models\Org;
use Illuminate\Support\Facades\Log;

/**
 * Class OrgService
 * @package App\Services\OrgService
 */
class OrgService extends ArtisanCloudService implements OrgServiceContract
{
    //
    public function __construct()
    {
        parent::__construct();
        $this->m_model = new Org();
    }


    /**
     * make a model
     *
     * @param array $arrayData
     *
     * @return mixed
     */
    public function makeBy(array $arrayData)
    {
        $this->m_model = $this->m_model->firstOrNew(
            [
                'name' => $arrayData['name'],
                'short_name' => $arrayData['short_name'],
            ],
            $arrayData
        );
//        dd($this->m_model);
        return $this->m_model;
    }

    /**
     * get list by
     *
     * @param User $user
     *
     * @return mixed
     */
    public function getListBy(User $user)
    {
        $orgs = $user->orgs()->get();
//        dd($orgs);
        return $orgs;
    }


    /**
     * Dispatch Job for create org by.
     *
     * @param User $user
     * @param string $orgName
     * @param string $shortName
     *
     * @return null|\Illuminate\Foundation\Bus\PendingDispatch
     */
    public static function dispatchCreateOrgBy(User $user, string $orgName, string $shortName, $standalone = true)
    {
        Log::info($user->mobile . ': Job ready to dispatch create org');

        return CreateOrg::dispatch($user, $orgName, $shortName, $standalone)
            ->onConnection('redis-tenant')
            ->onQueue('tenant-database');

        Log::info($user->mobile . ': Job finish to dispatch create org');
    }


    /**
     * set current session org
     *
     * @param Org $org
     *
     */

    public static function setSessionOrg(Org $org)
    {
        session(['org' => $org]);
    }

    /**
     * get current session org
     *
     * @return Org
     */
    public static function getSessionOrg(): ?Org
    {
        return session('org');
    }

}
