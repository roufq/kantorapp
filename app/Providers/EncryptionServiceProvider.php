<?php

namespace App\Providers;

use Illuminate\Cache\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

class EncryptionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Storage::extend('encrypted', function ($app, $config) {
            $adapter = new LocalFilesystemAdapter(
                $config['root']
            );

            return new FilesystemAdapter(
                new class($adapter) extends Filesystem {
                    public function __construct(private LocalFilesystemAdapter $adapter)
                    {
                        parent::__construct($adapter);
                    }

                    public function write(string $location, string $contents, array $config = []): void
                    {
                        $this->adapter->write($location, Crypt::encryptString($contents), $config);
                    }

                    public function read(string $location): string
                    {
                        $contents = $this->adapter->read($location);

                        return Crypt::decryptString($contents);
                    }
                },
                $adapter,
                $config
            );
        });

        Cache::extend('encrypted', function (Application $app, array $config) {
            $store = Cache::store($config['store']);

            return new class($store) extends Repository
            {
                public function __construct(private Repository $store)
                {
                    parent::__construct($store->getStore());
                }

                public function get($key, $default = null)
                {
                    $value = $this->store->get($key, $default);

                    if (is_null($value) || $value === $default) {
                        return $default;
                    }

                    try {
                        return Crypt::decrypt($value);
                    } catch (\Throwable) {
                        return $default;
                    }
                }

                public function set($key, $value, $ttl = null): bool
                {
                    return $this->store->set($key, Crypt::encrypt($value), $ttl);
                }
            };
        });
    }
}
