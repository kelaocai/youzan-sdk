<?php


namespace Hanson\Youzan\Platform;

use Hanson\Youzan\Core\AbstractAPI as BaseApi;

class AbstractAPI extends BaseApi
{

    public function parseJSON($method, $api, array $args)
    {
        $http = $this->getHttp();

        $result = json_decode(call_user_func_array([$http, $method],
            array_merge($args, ['access_token' => $this->getAccessToken()])), true);

        $this->checkAndThrow($result);

        return $result;
    }
}