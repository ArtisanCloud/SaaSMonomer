<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Services\OrgService\Http\Requests;


use App\Services\WorkzoneService\WorkzoneService;
use ArtisanCloud\SaaSFramework\Exceptions\BaseException;
use ArtisanCloud\SaaSFramework\Http\Requests\RequestBasic;
use ArtisanCloud\SaaSMonomer\Services\OrgService\Models\Org;


class RequestOrgReadItems extends RequestBasic
{
    protected WorkzoneService $userService;

    function __construct(WorkzoneService $userService)
    {
        parent::__construct();

        $this->userService = $userService;
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // check user can access this resource or not
        return $this->checkUserCanAccessesResource(Org::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

        ];
    }

    public function messages()
    {
        return [
            
        ];
    }

}
