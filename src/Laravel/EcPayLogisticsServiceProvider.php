<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Laravel;

use CarlLee\EcPayLogistics\Factories\OperationFactory;
use CarlLee\EcPayLogistics\Factories\OperationFactoryInterface;
use CarlLee\EcPayLogistics\FormBuilder;
use CarlLee\EcPayLogistics\Infrastructure\CheckMacEncoder;
use CarlLee\EcPayLogistics\Laravel\Services\LogisticsCoordinator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

/**
 * ECPay Logistics Laravel 服務提供者。
 */
class EcPayLogisticsServiceProvider extends ServiceProvider
{
    /**
     * 註冊服務。
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/ecpay-logistics.php',
            'ecpay-logistics'
        );

        $this->registerFactory();
        $this->registerFormBuilder();
        $this->registerEncoder();
        $this->registerCoordinator();
        $this->registerBindings();
    }

    /**
     * 啟動服務。
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/ecpay-logistics.php' => config_path('ecpay-logistics.php'),
            ], 'ecpay-logistics-config');
        }
    }

    /**
     * 註冊工廠。
     */
    protected function registerFactory(): void
    {
        $this->app->singleton(OperationFactoryInterface::class, function (Application $app) {
            $config = $app['config']->get('ecpay-logistics', []);

            return new OperationFactory([
                'merchant_id' => $config['merchant_id'] ?? '',
                'hash_key' => $config['hash_key'] ?? '',
                'hash_iv' => $config['hash_iv'] ?? '',
                'server_url' => $config['server'] ?? 'https://logistics-stage.ecpay.com.tw',
                'aliases' => $config['factory']['aliases'] ?? [],
                'initializers' => $this->resolveInitializers($config['factory']['initializers'] ?? []),
            ]);
        });

        $this->app->alias(OperationFactoryInterface::class, OperationFactory::class);
        $this->app->alias(OperationFactoryInterface::class, 'ecpay.logistics.factory');
    }

    /**
     * 註冊表單產生器。
     */
    protected function registerFormBuilder(): void
    {
        $this->app->singleton(FormBuilder::class, function (Application $app) {
            $server = $app['config']->get('ecpay-logistics.server', 'https://logistics-stage.ecpay.com.tw');

            return new FormBuilder($server);
        });

        $this->app->alias(FormBuilder::class, 'ecpay.logistics.form');
    }

    /**
     * 註冊編碼器。
     */
    protected function registerEncoder(): void
    {
        $this->app->singleton(CheckMacEncoder::class, function (Application $app) {
            $config = $app['config']->get('ecpay-logistics', []);

            return new CheckMacEncoder(
                $config['hash_key'] ?? '',
                $config['hash_iv'] ?? ''
            );
        });

        $this->app->alias(CheckMacEncoder::class, 'ecpay.logistics.encoder');
    }

    /**
     * 註冊協調器。
     */
    protected function registerCoordinator(): void
    {
        $this->app->singleton(LogisticsCoordinator::class, function (Application $app) {
            return new LogisticsCoordinator(
                $app->make(OperationFactoryInterface::class),
                $app->make(FormBuilder::class)
            );
        });

        $this->app->alias(LogisticsCoordinator::class, 'ecpay.logistics');
    }

    /**
     * 註冊便利綁定。
     */
    protected function registerBindings(): void
    {
        $bindings = $this->app['config']->get('ecpay-logistics.bindings', []);

        foreach ($bindings as $key => $alias) {
            $this->app->bind("ecpay.logistics.{$key}", function (Application $app) use ($alias) {
                return $app->make(OperationFactoryInterface::class)->make($alias);
            });
        }
    }

    /**
     * 解析初始化程式。
     *
     * @param array<int, string|callable> $initializers 初始化程式
     * @return array<int, callable>
     */
    protected function resolveInitializers(array $initializers): array
    {
        return array_map(function ($initializer) {
            if (is_string($initializer) && class_exists($initializer)) {
                return $this->app->make($initializer);
            }

            return $initializer;
        }, $initializers);
    }

    /**
     * 取得提供的服務。
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            OperationFactoryInterface::class,
            OperationFactory::class,
            FormBuilder::class,
            CheckMacEncoder::class,
            LogisticsCoordinator::class,
            'ecpay.logistics.factory',
            'ecpay.logistics.form',
            'ecpay.logistics.encoder',
            'ecpay.logistics',
        ];
    }
}
