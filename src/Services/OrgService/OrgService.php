<?php

namespace ArtisanCloud\SaaSMonomer\Services\OrgService;

use App\Models\User;
use ArtisanCloud\SaaSMonomer\Services\OrgService\Contracts\OrgServiceContract;
use ArtisanCloud\SaaSFramework\Services\ArtisanCloudService;
use ArtisanCloud\SaaSMonomer\Services\OrgService\Models\Org;

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
//                'created_by' => $arrayData['user_uuid'],
                'name' => $arrayData['name'],
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

}
