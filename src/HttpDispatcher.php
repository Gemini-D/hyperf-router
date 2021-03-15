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

use Gemini\Router\Annotation\WithoutMiddleware;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Router\Dispatched;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

class HttpDispatcher extends \Hyperf\Dispatcher\HttpDispatcher
{
    public function dispatch(...$params): ResponseInterface
    {
        /**
         * @param RequestInterface $request
         * @param array $middlewares
         * @param MiddlewareInterface $coreHandler
         */
        [$request, $middlewares, $coreHandler] = $params;

        $middlewares = static::withoutMiddlewares($request, $middlewares);

        return parent::dispatch($request, $middlewares, $coreHandler);
    }

    public static function withoutMiddlewares(ServerRequestInterface $request, array $middlewares): array
    {
        /** @var Dispatched $dispatched */
        $dispatched = $request->getAttribute(Dispatched::class);
        if ($dispatched->isFound()) {
            $callback = $dispatched->handler->callback;
            if ($handler = static::prepareHandler($callback)) {
                $class = $handler[0];
                $method = $handler[1] ?? null;
                $withoutMiddlewares = [];
                if ($annotation = AnnotationCollector::getClassAnnotation($class, WithoutMiddleware::class)) {
                    $withoutMiddlewares = $annotation->middlewares;
                }

                if ($method && $annotation = AnnotationCollector::getClassMethodAnnotation($class, $method)[WithoutMiddleware::class] ?? null) {
                    $withoutMiddlewares = array_merge($withoutMiddlewares, $annotation->middlewares);
                }

                $result = [];
                foreach ($middlewares as $middleware) {
                    if (is_string($middleware)) {
                        if (in_array($middleware, $withoutMiddlewares, true)) {
                            continue;
                        }
                    } elseif (is_object($middleware)) {
                        foreach ($withoutMiddlewares as $withoutMiddleware) {
                            if ($middleware instanceof $withoutMiddleware) {
                                continue;
                            }
                        }
                    }

                    $result[] = $middleware;
                }

                return $result;
            }
        }

        return $middlewares;
    }

    /**
     * @param mixed $handler
     */
    public static function prepareHandler($handler): ?array
    {
        if (is_string($handler)) {
            if (strpos($handler, '@') !== false) {
                return explode('@', $handler);
            }
            return explode('::', $handler);
        }
        if (is_array($handler) && isset($handler[0], $handler[1])) {
            return $handler;
        }
        return null;
    }
}
