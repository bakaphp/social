<?php

/**
 * Enabled providers. Order does matter.
 */

use Canvas\Providers\AppProvider;
use Canvas\Providers\CacheDataProvider;
use Canvas\Providers\DatabaseProvider as KanvasDatabaseProvider;
use Canvas\Providers\MapperProvider;
use Canvas\Providers\ModelsCacheProvider;
use Canvas\Providers\ModelsMetadataProvider;
use Canvas\Providers\RegistryProvider;
use Canvas\Providers\UserProvider;
use Canvas\Providers\ViewProvider;
use Kanvas\Social\Providers\DatabaseProvider as SocialDatabaseProvider;
use Kanvas\Social\Providers\QueueProvider;
use Kanvas\Social\Providers\RedisProvider;
use Kanvas\Social\Test\Support\Providers\ConfigProvider;

return [
    ConfigProvider::class,
    KanvasDatabaseProvider::class,
    SocialDatabaseProvider::class,
    ModelsMetadataProvider::class,
    RegistryProvider::class,
    QueueProvider::class,
    RedisProvider::class,
    AppProvider::class,
    UserProvider::class,
    CacheDataProvider::class,
    ModelsCacheProvider::class,
    MapperProvider::class,
    ViewProvider::class,
];
