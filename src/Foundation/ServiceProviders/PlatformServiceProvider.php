<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/2/22
 * Time: 22:34
 */

namespace Hanson\Youzan\Foundation\ServiceProviders;


use Hanson\Youzan\Platform\AccessToken;
use Hanson\Youzan\Platform\Platform;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PlatformServiceProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['platform'] = function ($pimple) {
            return new Platform($pimple['platform.access_token']);
        };
        $pimple['platform.access_token'] = function ($pimple) {
            return new AccessToken($pimple['config']['app_id'], $pimple['config']['secret']);
        };
    }
}