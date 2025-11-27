<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Parameter;

/**
 * 物流子類型。
 *
 * 包含超商和宅配的所有子類型。
 */
enum LogisticsSubType: string
{
    // ========== 超商 B2C ==========

    /**
     * 7-ELEVEN B2C。
     */
    case UNIMART = 'UNIMART';

    /**
     * 全家 B2C。
     */
    case FAMI = 'FAMI';

    /**
     * 萊爾富 B2C。
     */
    case HILIFE = 'HILIFE';

    /**
     * 7-ELEVEN 冷凍店取 B2C。
     */
    case UNIMART_FREEZE = 'UNIMARTFREEZE';

    // ========== 超商 C2C ==========

    /**
     * 7-ELEVEN C2C。
     */
    case UNIMART_C2C = 'UNIMARTC2C';

    /**
     * 全家 C2C。
     */
    case FAMI_C2C = 'FAMIC2C';

    /**
     * 萊爾富 C2C。
     */
    case HILIFE_C2C = 'HILIFEC2C';

    /**
     * OK超商 C2C。
     */
    case OKMART_C2C = 'OKMARTC2C';

    // ========== 宅配 ==========

    /**
     * 黑貓宅急便。
     */
    case TCAT = 'TCAT';

    /**
     * 中華郵政。
     */
    case POST = 'POST';

    /**
     * 取得說明。
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::UNIMART => '7-ELEVEN (B2C)',
            self::FAMI => '全家 (B2C)',
            self::HILIFE => '萊爾富 (B2C)',
            self::UNIMART_FREEZE => '7-ELEVEN 冷凍店取 (B2C)',
            self::UNIMART_C2C => '7-ELEVEN (C2C)',
            self::FAMI_C2C => '全家 (C2C)',
            self::HILIFE_C2C => '萊爾富 (C2C)',
            self::OKMART_C2C => 'OK超商 (C2C)',
            self::TCAT => '黑貓宅急便',
            self::POST => '中華郵政',
        };
    }

    /**
     * 是否為 C2C。
     *
     * @return bool
     */
    public function isC2C(): bool
    {
        return match ($this) {
            self::UNIMART_C2C,
            self::FAMI_C2C,
            self::HILIFE_C2C,
            self::OKMART_C2C => true,
            default => false,
        };
    }

    /**
     * 是否為 B2C。
     *
     * @return bool
     */
    public function isB2C(): bool
    {
        return match ($this) {
            self::UNIMART,
            self::FAMI,
            self::HILIFE,
            self::UNIMART_FREEZE => true,
            default => false,
        };
    }

    /**
     * 是否為超商。
     *
     * @return bool
     */
    public function isCvs(): bool
    {
        return match ($this) {
            self::TCAT, self::POST => false,
            default => true,
        };
    }

    /**
     * 是否為宅配。
     *
     * @return bool
     */
    public function isHome(): bool
    {
        return match ($this) {
            self::TCAT, self::POST => true,
            default => false,
        };
    }

    /**
     * 取得對應的物流類型。
     *
     * @return LogisticsType
     */
    public function getLogisticsType(): LogisticsType
    {
        return $this->isHome() ? LogisticsType::HOME : LogisticsType::CVS;
    }
}
