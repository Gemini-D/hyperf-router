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
