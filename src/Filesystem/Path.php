<?php

declare(strict_types=1);

namespace Pollen\Kernel\Filesystem;

use Pollen\Filesystem\LocalFilesystem;
use Pollen\Filesystem\LocalFilesystemAdapter;
use Pollen\Filesystem\LocalFilesystemInterface;
use Pollen\Filesystem\StorageManager;
use Pollen\Support\Filesystem as fs;

class Path extends StorageManager implements PathInterface
{
    /**
     * @inheritDoc
     */
    public function diskBase(): LocalFilesystemInterface
    {
        if (!$disk = $this->disk('base')) {
            $disk = $this->mount('base', ROOT_PATH);
        }

        return $disk;
    }

    /**
     * @inheritDoc
     */
    public function diskCache(): LocalFilesystemInterface
    {
        if (!$disk = $this->disk('cache')) {
            $disk = $this->mount('cache', $this->getStoragePath('/cache'));
        }

        return $disk;
    }

    /**
     * @inheritDoc
     */
    public function diskConfig(): LocalFilesystemInterface
    {
        if (!$disk = $this->disk('config')) {
            $disk = $this->mount('config', !$this->isWp()
                ? $this->getBasePath('config') : get_template_directory() . '/config'
            );
        }

        return $disk;
    }

    /**
     * @inheritDoc
     */
    public function diskLog(): LocalFilesystemInterface
    {
        if (!$disk = $this->disk('log')) {
            $disk = $this->mount('log', $this->getStoragePath('/log'));
        }

        return $disk;
    }

    /**
     * @inheritDoc
     */
    public function diskPathFromBase(LocalFilesystemInterface $disk, string $path = '', bool $absolute = true): ?string
    {
        $path = preg_replace('/^' . preg_quote($this->getBasePath(), fs::DS) . '/', '', $disk->getAbsolutePath($path), 1, $n);

        return $n === 1 ? $this->getBasePath($path, $absolute) : null;
    }

    /**
     * @inheritDoc
     */
    public function diskPublic(): LocalFilesystemInterface
    {
        if (!$disk = $this->disk('public')) {
            $disk = $this->mount('public', !$this->isWp() ? $this->getBasePath('/public') : ABSPATH);
        }

        return $disk;
    }

    /**
     * @inheritDoc
     */
    public function diskStorage(): LocalFilesystemInterface
    {
        if (!$disk = $this->disk('storage')) {
            $disk = $this->mount(
                'storage', !$this->isWp() ? $this->getBasePath('storage') : WP_CONTENT_DIR . '/uploads'
            );
        }

        return $disk;
    }

    /**
     * @inheritDoc
     */
    public function diskTheme(): LocalFilesystemInterface
    {
        if (!$disk = $this->disk('theme')) {
            $disk = $this->mount('theme', get_template_directory());
        }

        return $disk;
    }

    /**
     * @inheritDoc
     */
    public function getBasePath(string $path = '', bool $absolute = true): string
    {

        return $this->normalize($absolute ? $this->diskBase()->getAbsolutePath($path) : $path);
    }

    /**
     * @inheritDoc
     */
    public function getCachePath(string $path = '', bool $absolute = true): string
    {
        return $this->diskPathFromBase($this->diskCache(), $path, $absolute);
    }

    /**
     * @inheritDoc
     */
    public function getConfigPath(string $path = '', bool $absolute = true): string
    {
        return $this->diskPathFromBase($this->diskConfig(), $path, $absolute);
    }

    /**
     * @inheritDoc
     */
    public function getLogPath(string $path = '', bool $absolute = true): string
    {
        return $this->diskPathFromBase($this->diskLog(), $path, $absolute);
    }

    /**
     * @inheritDoc
     */
    public function getPublicPath(string $path = '', bool $absolute = true): string
    {
        return $this->diskPathFromBase($this->diskPublic(), $path, $absolute);
    }

    /**
     * @inheritDoc
     */
    public function getStoragePath(string $path = '', bool $absolute = true): string
    {
        return $this->diskPathFromBase($this->diskStorage(), $path, $absolute);
    }

    /**
     * @inheritDoc
     */
    public function getThemePath(string $path = '', bool $absolute = true): string
    {
        return $this->diskPathFromBase($this->diskTheme(), $path, $absolute);
    }

    /**
     * @inheritDoc
     */
    public function isWp(): bool
    {
        return defined('ABSPATH') && ($this->normalize(ABSPATH) === $this->normalize($this->getBasePath()));
    }

    /**
     * @inheritDoc
     */
    public function mount(string $name, string $root, array $config = []): LocalFilesystemInterface
    {
        $links = ($config['links'] ?? null) === 'skip'
            ? LocalFilesystemAdapter::SKIP_LINKS
            : LocalFilesystemAdapter::DISALLOW_LINKS;

        $adapter = new LocalFilesystemAdapter($root, null, LOCK_EX, $links);

        /*
        if ($cache = $config['cache'] ?? true) {
            $adapter = $cache instanceof CacheInterface
                ? new CachedAdapter($adapter, $cache)
                : new CachedAdapter($adapter, new MemoryStore());
        }
        */

        $filesystem = new LocalFilesystem($adapter, [
            'disable_asserts' => true,
            'case_sensitive' => true
        ]);

        $this->addDisk($name, $filesystem);

        return $filesystem;
    }

    /**
     * @inheritDoc
     */
    public function normalize(string $path): string
    {
        return fs::DS . ltrim(rtrim($path, fs::DS), fs::DS);
    }

    /**
     * @inheritDoc
     */
    public function relPathFromBase(string $pathname): ?string
    {
        $path = preg_replace('/^' . preg_quote($this->getBasePath(), fs::DS) . '/', '', $pathname, 1, $n);

        return $n === 1 ? $this->getBasePath($path, false): null;
    }
}