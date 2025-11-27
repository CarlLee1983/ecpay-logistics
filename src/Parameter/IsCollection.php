<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Parameter;

/**
 * 是否代收貨款。
 */
enum IsCollection: string
{
    /**
     * 不代收貨款。
     */
    case NO = 'N';

    /**
     * 代收貨款。
     */
    case YES = 'Y';

    /**
     * 取得說明。
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::NO => '不代收貨款',
            self::YES => '代收貨款',
        };
    }

    /**
     * 是否為代收貨款。
     *
     * @return bool
     */
    public function isCollection(): bool
    {
        return $this === self::YES;
    }
}
