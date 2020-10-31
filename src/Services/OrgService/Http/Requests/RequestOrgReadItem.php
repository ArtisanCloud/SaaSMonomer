<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Services\OrgService\Http\Requests;

use App\Models\Tenants\Org;
use ArtisanCloud\SaaSMonomer\Services\OrgService\OrgService;
use ArtisanCloud\SaaSFramework\Exceptions\BaseException;
use ArtisanCloud\SaaSFramework\Http\Requests\RequestBasic;
use Illuminate\Validation\Rule;

class RequestOrgReadItem extends RequestBasic
{
    protected OrgService $orgService;

    function __construct(OrgService $orgService)
    {
        parent::__construct();

        $this->orgService = $orgService;
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $org = $this->orgService->getModelByUUID($this->input('uuid'));
//        dd($org);
        if(is_null($org)){
            throw new BaseException(API_ERR_CODE_ORG_NOT_EXIST);
        }

        // check user can access this resource or not
        $bResult = $this->checkUserOwnsResource($org);
        if ($bResult) {
            $this->getInputSource()->set('org', $org);
        }
        return $bResult;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'uuid' => [
                'required',
                'uuid'
            ]
        ];
    }

    public function messages()
    {
        return [
            'uuid.required' => __("{$this->m_module}.required"),
            'uuid.uuid' => __("{$this->m_module}.uuid"),
//            'uuid.exists' => __("{$this->m_module}.exists"),
        ];
    }

}
