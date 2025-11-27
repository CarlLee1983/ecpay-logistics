<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Laravel\Facades;

use CarlLee\EcPayLogistics\Content;
use CarlLee\EcPayLogistics\Laravel\Services\LogisticsCoordinator;
use Illuminate\Support\Facades\Facade;

/**
 * ECPay Logistics Facade。
 *
 * @method static Content make(string $target, array $parameters = [])
 * @method static array openStoreMap(string $tradeNo, string $subType, bool $isCollection = false, string $serverReplyUrl = null)
 * @method static \CarlLee\EcPayLogistics\Response createCvsOrder(array $data)
 * @method static \CarlLee\EcPayLogistics\Response createHomeOrder(array $data)
 * @method static \CarlLee\EcPayLogistics\Response queryOrder(string $logisticsId)
 *
 * @see \CarlLee\EcPayLogistics\Laravel\Services\LogisticsCoordinator
 */
class EcPayLogistics extends Facade
{
    /**
     * 取得元件的註冊名稱。
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return LogisticsCoordinator::class;
    }
}
