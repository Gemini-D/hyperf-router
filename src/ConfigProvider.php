<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Gemini\Router;

use Gemini\Router\Listener\HttpDispatcherCheckListener;
use Gemini\Router\Listener\InitRouteCollectorListener;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'listeners' => [
                InitRouteCollectorListener::class,
                HttpDispatcherCheckListener::class,
            ],
            'dependencies' => [
                \Hyperf\Dispatcher\HttpDispatcher::class => HttpDispatcher::class,
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
        ];
    }
}
