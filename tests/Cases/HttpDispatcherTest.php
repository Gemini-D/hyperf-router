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
namespace HyperfTest\Cases;

use FastRoute\Dispatcher;
use Gemini\Router\Annotation\WithoutMiddleware;
use Gemini\Router\HttpDispatcher;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\HttpServer\Router\Handler;
use Mockery as m;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @internal
 * @coversNothing
 */
class HttpDispatcherTest extends AbstractTestCase
{
    protected function tearDown(): void
    {
        AnnotationCollector::clear();
        parent::tearDown();
    }

    public function testWithoutMiddlewaresWithNotFound()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getAttribute')->with(Dispatched::class)->andReturn(new Dispatched([Dispatcher::NOT_FOUND]));

        $middlewares = HttpDispatcher::withoutMiddlewares($request, $assert = ['FooMiddleware', 'BarMiddleware']);

        $this->assertSame($assert, $middlewares);
    }

    public function testWithoutMiddlewaresWithFound()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getAttribute')->with(Dispatched::class)->andReturn(new Dispatched([
            Dispatcher::FOUND,
            new Handler('IndexController@index', 'index', []),
            [],
        ]));

        AnnotationCollector::set('IndexController._c.' . WithoutMiddleware::class, new WithoutMiddleware(['value' => 'FooMiddleware']));

        $middlewares = HttpDispatcher::withoutMiddlewares($request, ['FooMiddleware', 'BarMiddleware']);

        $this->assertSame(['BarMiddleware'], $middlewares);
    }

    public function testWithoutMiddlewaresInWebsocket()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getAttribute')->with(Dispatched::class)->andReturn(new Dispatched([
            Dispatcher::FOUND,
            new Handler('IndexController', 'index', []),
            [],
        ]));

        AnnotationCollector::set('IndexController._c.' . WithoutMiddleware::class, new WithoutMiddleware(['value' => 'FooMiddleware']));

        $middlewares = HttpDispatcher::withoutMiddlewares($request, ['FooMiddleware', 'BarMiddleware']);

        $this->assertSame(['BarMiddleware'], $middlewares);
    }
}
