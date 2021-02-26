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
namespace Gemini\Router\Listener;

use Gemini\Router\HttpDispatcher as GeminiHttpDispatcher;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Dispatcher\HttpDispatcher;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use Psr\Container\ContainerInterface;

class HttpDispatcherCheckListener implements ListenerInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    public function process(object $event)
    {
        $dispatcher = $this->container->get(HttpDispatcher::class);

        if (! $dispatcher instanceof GeminiHttpDispatcher) {
            $logger = $this->container->get(StdoutLoggerInterface::class);
            $logger->warning(
                sprintf(
                    'HttpDispatcher is not instanceof %s, please set %s => %s in dependencies.',
                    GeminiHttpDispatcher::class,
                    HttpDispatcher::class,
                    GeminiHttpDispatcher::class
                )
            );
        }
    }
}
