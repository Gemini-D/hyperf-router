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
use Gemini\Router\RouteContext;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\HttpServer\Router\Handler;
use Hyperf\Utils\Context;
use Mockery;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @internal
 * @coversNothing
 */
class RouteContextTest extends AbstractTestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        Context::set(ServerRequestInterface::class, null);
    }

    public function testGetRouteName()
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getAttribute')->with(Dispatched::class)->andReturnUsing(function () {
            return new Dispatched([
                Dispatcher::FOUND,
                new Handler([], '/', ['name' => 'index']),
                [
                    'id' => uniqid(),
                ],
            ]);
        });
        Context::set(ServerRequestInterface::class, $request);
        $context = new RouteContext();
        $this->assertSame('index', $context->getRouteName());
    }
}
