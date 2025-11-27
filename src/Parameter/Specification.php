<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Parameter;

/**
 * 包裹規格。
 *
 * 宅配專用參數。
 */
enum Specification: string
{
    /**
     * 60cm。
     */
    case SIZE_60 = '0001';

    /**
     * 90cm。
     */
    case SIZE_90 = '0002';

    /**
     * 120cm。
     */
    case SIZE_120 = '0003';

    /**
     * 150cm。
     */
    case SIZE_150 = '0004';

    /**
     * 取得說明。
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::SIZE_60 => '60cm',
            self::SIZE_90 => '90cm',
            self::SIZE_120 => '120cm',
            self::SIZE_150 => '150cm',
        };
    }

    /**
     * 取得最大邊長（公分）。
     *
     * @return int
     */
    public function maxSize(): int
    {
        return match ($this) {
            self::SIZE_60 => 60,
            self::SIZE_90 => 90,
            self::SIZE_120 => 120,
            self::SIZE_150 => 150,
        };
    }
}
