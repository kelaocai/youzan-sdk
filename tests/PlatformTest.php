<?php


namespace Hanson\Youzan\Tests;


class PlatformTest extends YouzanBaseTest
{

    public function testToken()
    {
        echo $this->app['platform.access_token']->getToken();
    }
}