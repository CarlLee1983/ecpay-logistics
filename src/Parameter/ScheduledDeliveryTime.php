<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Parameter;

/**
 * 預約配送時段。
 *
 * 宅配專用參數。
 */
enum ScheduledDeliveryTime: string
{
    /**
     * 不限時。
     */
    case UNLIMITED = '4';

    /**
     * 13 前。
     */
    case BEFORE_13 = '1';

    /**
     * 14~18。
     */
    case BETWEEN_14_18 = '2';

    /**
     * 取得說明。
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::UNLIMITED => '不限時',
            self::BEFORE_13 => '13 前',
            self::BETWEEN_14_18 => '14~18',
        };
    }
}
