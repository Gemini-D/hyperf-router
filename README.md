# Some features for Hyperf Router.

[![PHPUnit](https://github.com/Gemini-D/hyperf-router/actions/workflows/test.yml/badge.svg)](https://github.com/Gemini-D/hyperf-router/actions/workflows/test.yml)

```
composer require gemini/hyperf-router
```

## 路由名字

### 设置路由名字

- 路由文件模式

```php
<?php
use Hyperf\HttpServer\Router\Router;

Router::get('/', 'App\Controller\IndexController::index', ['name' => 'index']);
Router::get('/user/{id:\d+}', 'App\Controller\UserController::info', ['name' => 'user.info']);
Router::get('/user', 'App\Controller\UserController::index', ['name' => 'user.list']);
```

- 注解模式
  
```php
<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use function Gemini\Router\route;

/**
 * @Controller(prefix="/user")
 */
class UserController
{
    /**
     * @GetMapping(path="info/{id:\d+}", options={"name": "user.info"})
     */
    public function info(int $id)
    {
        return [
            'id' => $id,
            'next_path' => route('user.info', ['id' => ++$id]),
        ];
    }
}

```

### 根据路有名字读取路径

```php
<?php

use function \Gemini\Router\route;

var_dump(route('index'));
var_dump(route('user.info', [1]));
```


## 排除中间件

组件提供了 `Gemini\Router\Annotation\WithoutMiddleware` 注解，可以方便用户排除不想被触发的中间件。

当在控制器中设置时，会使控制器下所有的方法生效。

当在方法中设置时，只会使当前方法生效。

> 注意使用 ::class 时，不要忘了引入命名空间

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use Gemini\Router\Annotation\WithoutMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use function Gemini\Router\route;
use Han\Utils\Middleware\DebugMiddleware;

/**
 * @Controller(prefix="/user")
 * @WithoutMiddleware(DebugMiddleware::class)
 */
class UserController
{
    /**
     * @GetMapping(path="info/{id:\d+}", options={"name": "user.info"})
     * @WithoutMiddleware({Debug2Middleware::class, Debug3Middleware::class})
     */
    public function info(int $id)
    {
        return [
            'id' => $id,
            'next_path' => route('user.info', ['id' => ++$id]),
        ];
    }
}

```
