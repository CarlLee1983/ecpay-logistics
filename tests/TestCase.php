<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * 測試用特店編號。
     */
    protected string $merchantId = '2000132';

    /**
     * 測試用 HashKey。
     */
    protected string $hashKey = '5294y06JbISpM5x9';

    /**
     * 測試用 HashIV。
     */
    protected string $hashIV = 'v77hoKGq4kWxNNIS';

    /**
     * 測試用伺服器網址。
     */
    protected string $serverUrl = 'https://logistics-stage.ecpay.com.tw';
}
