<?php


namespace Hanson\Youzan\Platform;


use Hanson\Youzan\Foundation\Application;
use Illuminate\Support\Str;

class Platform
{

    /**
     * @var Application
     */
    private $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function createAuthorizerApplication()
    {
        return $this->fetch('app', function ($app) {
            $app['access_token'] = $this->fetch('access_token');
        });
    }

    /**
     * Fetches from pimple container.
     *
     * @param string        $key
     * @param callable|null $callable
     *
     * @return mixed
     */
    public function fetch($key, callable $callable = null)
    {
        $instance = $this->$key;

        if (!is_null($callable)) {
            $callable($instance);
        }

        return $instance;
    }

    public function __get($key)
    {
        $className = basename(str_replace('\\', '/', static::class));

        $name = Str::snake($className).'.'.$key;

        return $this->application->offsetGet($name);
    }

}