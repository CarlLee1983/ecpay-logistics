<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Exceptions;

use Exception;

/**
 * 物流相關例外。
 */
class LogisticsException extends Exception
{
    /**
     * 驗證欄位失敗。
     *
     * @param string $field 欄位名稱
     * @param string $message 錯誤訊息
     * @return static
     */
    public static function invalid(string $field, string $message): static
    {
        return new static("欄位 {$field} 驗證失敗：{$message}");
    }

    /**
     * 必填欄位缺失。
     *
     * @param string $field 欄位名稱
     * @return static
     */
    public static function required(string $field): static
    {
        return new static("欄位 {$field} 為必填");
    }

    /**
     * 欄位超過長度限制。
     *
     * @param string $field 欄位名稱
     * @param int $maxLength 最大長度
     * @return static
     */
    public static function tooLong(string $field, int $maxLength): static
    {
        return new static("欄位 {$field} 超過最大長度 {$maxLength}");
    }

    /**
     * CheckMacValue 驗證失敗。
     *
     * @return static
     */
    public static function checkMacValueFailed(): static
    {
        return new static('CheckMacValue 驗證失敗');
    }

    /**
     * API 回傳錯誤。
     *
     * @param string $code 錯誤代碼
     * @param string $message 錯誤訊息
     * @return static
     */
    public static function apiError(string $code, string $message): static
    {
        return new static("API 錯誤 [{$code}]：{$message}", (int) $code);
    }

    /**
     * HTTP 請求失敗。
     *
     * @param string $message 錯誤訊息
     * @return static
     */
    public static function httpError(string $message): static
    {
        return new static("HTTP 請求失敗：{$message}");
    }

    /**
     * 不支援的操作。
     *
     * @param string $operation 操作名稱
     * @return static
     */
    public static function unsupportedOperation(string $operation): static
    {
        return new static("不支援的操作：{$operation}");
    }

    /**
     * 不支援的物流類型。
     *
     * @param string $type 物流類型
     * @return static
     */
    public static function unsupportedLogisticsType(string $type): static
    {
        return new static("不支援的物流類型：{$type}");
    }
}
