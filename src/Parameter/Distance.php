<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Parameter;

/**
 * 配送距離。
 *
 * 宅配專用參數。
 */
enum Distance: string
{
    /**
     * 同縣市。
     */
    case SAME = '00';

    /**
     * 外縣市。
     */
    case OTHER = '01';

    /**
     * 離島。
     */
    case ISLAND = '02';

    /**
     * 取得說明。
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::SAME => '同縣市',
            self::OTHER => '外縣市',
            self::ISLAND => '離島',
        };
    }
}
