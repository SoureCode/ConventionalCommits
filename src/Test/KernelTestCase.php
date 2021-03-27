<?php

namespace SoureCode\ConventionalCommits\Test;

use PHPUnit\Framework\TestCase;
use SoureCode\ConventionalCommits\Kernel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Service\ResetInterface;

class KernelTestCase extends TestCase
{

    protected static ?Kernel $kernel = null;

    protected static bool $booted = false;

    private static ?ContainerInterface $kernelContainer = null;

    protected static function getContainer(): ContainerInterface
    {
        if (!static::$booted) {
            static::bootKernel();
        }

        return self::$kernelContainer;
    }

    protected static function createKernel(array $options = []): Kernel
    {
        if (isset($options['environment'])) {
            $env = $options['environment'];
        } elseif (isset($_ENV['APP_ENV'])) {
            $env = $_ENV['APP_ENV'];
        } elseif (isset($_SERVER['APP_ENV'])) {
            $env = $_SERVER['APP_ENV'];
        } else {
            $env = 'test';
        }

        if (isset($options['debug'])) {
            $debug = $options['debug'];
        } elseif (isset($_ENV['APP_DEBUG'])) {
            $debug = $_ENV['APP_DEBUG'];
        } elseif (isset($_SERVER['APP_DEBUG'])) {
            $debug = $_SERVER['APP_DEBUG'];
        } else {
            $debug = true;
        }

        return new Kernel($env, $debug);
    }

    protected static function bootKernel(array $options = []): Kernel
    {
        static::ensureKernelShutdown();

        static::$kernel = static::createKernel($options);

        static::$kernel->boot();

        static::$booted = true;

        self::$kernelContainer = static::$kernel->getContainer();

        return static::$kernel;
    }

    protected function tearDown(): void
    {
        static::ensureKernelShutdown();

        static::$kernel = null;
        static::$booted = false;
    }

    protected static function ensureKernelShutdown(): void
    {
        if (null !== static::$kernel) {
            static::$kernel->shutdown();
            static::$booted = false;
        }

        if (self::$kernelContainer instanceof ResetInterface) {
            self::$kernelContainer->reset();
        }

        self::$kernelContainer = null;
    }

}
