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
namespace Gemini\Router\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * @Annotation
 * @Target({"ALL"})
 */
class WithoutMiddleware extends AbstractAnnotation
{
    /**
     * @var string[]
     */
    public $middlewares = [];

    public function __construct($value = null)
    {
        $this->middlewares = (array) $value['value'];
    }
}
