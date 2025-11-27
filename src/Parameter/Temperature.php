<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Parameter;

/**
 * 溫層。
 *
 * 宅配專用參數。
 */
enum Temperature: string
{
    /**
     * 常溫。
     */
    case ROOM = '0001';

    /**
     * 冷藏。
     */
    case REFRIGERATION = '0002';

    /**
     * 冷凍。
     */
    case FREEZE = '0003';

    /**
     * 取得說明。
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::ROOM => '常溫',
            self::REFRIGERATION => '冷藏',
            self::FREEZE => '冷凍',
        };
    }
}
