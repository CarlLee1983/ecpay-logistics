<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Parameter;

/**
 * 裝置類型。
 *
 * 電子地圖專用參數。
 */
enum Device: int
{
    /**
     * 桌機版（預設）。
     */
    case PC = 0;

    /**
     * 手機版。
     */
    case MOBILE = 1;

    /**
     * 取得說明。
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::PC => '桌機版',
            self::MOBILE => '手機版',
        };
    }
}
