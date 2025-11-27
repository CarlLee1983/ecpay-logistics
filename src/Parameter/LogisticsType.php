<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Parameter;

/**
 * 物流類型。
 */
enum LogisticsType: string
{
    /**
     * 超商取貨。
     */
    case CVS = 'CVS';

    /**
     * 宅配。
     */
    case HOME = 'Home';

    /**
     * 取得說明。
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::CVS => '超商取貨',
            self::HOME => '宅配',
        };
    }
}
