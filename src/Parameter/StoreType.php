<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Parameter;

/**
 * 門市類型。
 *
 * 用於篩選門市清單。
 */
enum StoreType: string
{
    /**
     * 僅取出店（預設）。
     */
    case PICKUP_ONLY = '01';

    /**
     * 取出店＋退貨店。
     */
    case PICKUP_AND_RETURN = '02';

    /**
     * 僅退貨店。
     */
    case RETURN_ONLY = '03';

    /**
     * 取得說明。
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::PICKUP_ONLY => '僅取出店',
            self::PICKUP_AND_RETURN => '取出店＋退貨店',
            self::RETURN_ONLY => '僅退貨店',
        };
    }
}
