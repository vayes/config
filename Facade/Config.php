<?php

namespace Vayes\Config\Facade;

use Vayes\Config\Provider\ConfigProvider;
use Vayes\Facade\Facade;

/**
 * @method static item(string $item, ?string $segment = null)
 * @method static getCachedYamlPath($file, string $cachePath, ?string $customSegment = null): string
 */
class Config extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return ConfigProvider::class;
    }
}
