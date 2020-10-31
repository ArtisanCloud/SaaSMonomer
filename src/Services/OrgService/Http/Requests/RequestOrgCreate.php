<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Services\OrgService\Http\Requests;


use ArtisanCloud\SaaSMonomer\Services\OrgService\Models\Org;
use ArtisanCloud\SaaSFramework\Http\Requests\RequestBasic;
use Illuminate\Validation\Rule;

class RequestOrgCreate extends RequestBasic
{
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

            'name' => [
                'required',
                'string',
                'max:20',
            ],
        ];
    }

    public function messages()
    {
        return [

            'name.required' => __("{$this->m_module}.required"),


        ];
    }

}
