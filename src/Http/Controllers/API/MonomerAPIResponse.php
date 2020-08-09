<?php


namespace ArtisanCloud\SaaSMonomer\Http\Controllers\API;

use ArtisanCloud\SaaSMonomer\Models\ClientProfile;
use Illuminate\Contracts\Support\Responsable;

class MonomerAPIResponse implements Responsable
{
    private $returnCode;

    private $returnMessage;

    private $resultCode;

    private $resultMessage;

    private $data;

    public static function success($data = null)
    {
        $response = new self();
        $response->setReturnCode(API_RETURN_CODE_INIT);
        $response->setReturnMessage(trans("messages." . API_RETURN_CODE_INIT));
        $response->setResultCode(API_RESULT_CODE_INIT);
        $response->setResultMessage(trans("messages." . API_RESULT_CODE_INIT));
        $response->setData($data);

        return $response->toJson();
    }

    public static function error($resultCode, $resultMessage = "", $returnMessage=null)
    {
        $response = new self();
        $response->setReturnCode(API_RETURN_CODE_ERROR);
        $response->setReturnMessage(trans("messages." . API_RETURN_CODE_ERROR));
        $response->setResultCode($resultCode);

        // given return message
        if (!empty($returnMessage)) {
            $response->setReturnMessage($returnMessage);
        }

        // given result message
        if (empty($resultMessage)) {
            $response->setResultMessage(trans("messages." . $resultCode));
        } else {
            $response->setResultMessage($resultMessage);
        }
        return $response->toJson();
    }


    /**
     * @return mixed
     */
    public function getReturnCode()
    {
        return $this->returnCode;
    }

    /**
     * @param mixed $returnCode
     */
    public function setReturnCode($returnCode)
    {
        $this->returnCode = $returnCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReturnMessage()
    {
        return $this->returnMessage;
    }

    /**
     * @param mixed $returnMessage
     */
    public function setReturnMessage($returnMessage)
    {
        $this->returnMessage = $returnMessage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResultCode()
    {
        return $this->resultCode;
    }

    /**
     * @param mixed $resultCode
     */
    public function setResultCode($resultCode)
    {
        $this->resultCode = $resultCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResultMessage()
    {
        return $this->resultMessage;
    }

    /**
     * @param mixed $resultMessage
     */
    public function setResultMessage($resultMessage)
    {
        $this->resultMessage = $resultMessage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function toJson()
    {
        $response = [
            'meta' => [
                'return_code' => $this->returnCode,
                'return_message' => $this->returnMessage,
                'result_code' => $this->resultCode,
                'result_message' => $this->resultMessage,
                'timezone' => ClientProfile::TIMEZONE,
                'locale' => ClientProfile::getSessionLocale(),
            ],
        ];
        if (!is_null($this->data)) {
            $response['data'] = $this->data;
        }
        return response()->json($response);
    }

    /**
     * @inheritDoc
     */
    public function toResponse($request)
    {
        return $this->toJson();
    }

    /**
     * Throw json response.
     *
     * @param  null
     * @return Response response
     */
    public function throwJSONResponse()
    {
        header('Content-Type: application/json');
        echo $this->toResponse()->content();
        exit();
    }
}
