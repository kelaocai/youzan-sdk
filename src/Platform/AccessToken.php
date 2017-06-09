<?php


namespace Hanson\Youzan\Platform;


use Doctrine\Common\Cache\FilesystemCache;
use Hanson\Youzan\Core\Http;

class AccessToken
{

    /**
     * 有赞云颁发给开发者的应用ID.
     *
     * @var string
     */
    protected $clientId;

    /**
     * 有赞云颁发给开发者的应用secret.
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * @var Http
     */
    private $http;

    /**
     * @var FilesystemCache
     */
    private $cache;

    private $cacheKey = 'hanson.youzan.platform.';

    const TOKEN_API = 'https://open.youzan.com/oauth/token';
    const OAUTH_ENTRY_API = 'https://open.youzan.com/api/oauthentry/';

    public function __construct($clientId, $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * get token.
     *
     * @param bool $force
     * @return false|mixed
     * @throws \Exception
     */
    public function getToken($force = false)
    {
        $cached = $this->getCache()->fetch($this->cacheKey.$this->clientId);

        if (!$cached || $force) {
            $token = $this->getTokenFromServer();

            if (!isset($token['accessToken']) || !$token['accessToken']) {
                throw new \Exception($token['error_description'], $token['error']);
            }

            $this->getCache()->save($this->cacheKey.$this->clientId, $token['accessToken'], $token['expires']);

            return $token['accessToken'];
        }

        return $cached;
    }

    /**
     * get token from remote server.
     *
     * @param null $refreshToken
     * @return array
     */
    public function getTokenFromServer($refreshToken = null)
    {
        $params = ['client_id' => $this->clientId, 'client_secret' => $this->clientSecret];

        $params = !$refreshToken ? array_merge($params, ['grant_type' => 'authorize_platform']) : array_merge($params, [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]);

        return json_decode($this->getHttp()->post(static::TOKEN_API, $params), true);
    }

    /**
     * @param $method
     * @param $args
     * @param string $version
     * @return array
     */
    public function signatureParam($method, $args, $version = '3.0.0')
    {
        $path = strrchr($args[0], '/');

        $args[0] = static::OAUTH_ENTRY_API.$method.'/'.'3.0.0'.$path;

        $args[1]['access_token'] = $this->getToken();

        return $args;
    }


    /**
     * get a http instance.
     *
     * @return Http
     */
    private function getHttp()
    {
        return $this->http ?: $this->http = new Http();
    }

    /**
     * set a cache.
     *
     * @param $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * get a cache.
     *
     * @return FilesystemCache
     */
    public function getCache()
    {
        return $this->cache ?: $this->cache = new FilesystemCache(sys_get_temp_dir());
    }

}