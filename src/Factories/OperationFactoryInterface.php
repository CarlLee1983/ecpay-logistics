<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Factories;

use CarlLee\EcPayLogistics\Content;

/**
 * 操作工廠介面。
 */
interface OperationFactoryInterface
{
    /**
     * 建立操作物件。
     *
     * @param string $target 目標類別或別名
     * @param array<int, mixed> $parameters 建構參數
     * @return Content
     */
    public function make(string $target, array $parameters = []): Content;

    /**
     * 註冊自訂解析器。
     *
     * @param string $alias 別名
     * @param callable $resolver 解析器
     */
    public function extend(string $alias, callable $resolver): void;

    /**
     * 註冊別名。
     *
     * @param string $alias 別名
     * @param string $class 類別名稱
     */
    public function alias(string $alias, string $class): void;

    /**
     * 新增初始化程式。
     *
     * @param callable $initializer 初始化程式
     */
    public function addInitializer(callable $initializer): void;

    /**
     * 設定憑證。
     *
     * @param string $merchantId 特店編號
     * @param string $hashKey HashKey
     * @param string $hashIV HashIV
     */
    public function setCredentials(string $merchantId, string $hashKey, string $hashIV): void;
}
