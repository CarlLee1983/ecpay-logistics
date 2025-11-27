<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Infrastructure;

use CarlLee\EcPayLogistics\Exceptions\LogisticsException;

/**
 * CheckMacValue 編碼器。
 *
 * 負責計算與驗證綠界物流 API 的 CheckMacValue。
 * 物流 API 使用 MD5 加密機制。
 */
class CheckMacEncoder
{
    /**
     * HashKey。
     */
    private readonly string $hashKey;

    /**
     * HashIV。
     */
    private readonly string $hashIV;

    /**
     * 建立編碼器。
     *
     * @param string $hashKey HashKey
     * @param string $hashIV HashIV
     * @throws \InvalidArgumentException 當 HashKey 或 HashIV 為空時
     */
    public function __construct(string $hashKey, string $hashIV)
    {
        if ($hashKey === '') {
            throw new \InvalidArgumentException('HashKey 不可為空');
        }

        if ($hashIV === '') {
            throw new \InvalidArgumentException('HashIV 不可為空');
        }

        $this->hashKey = $hashKey;
        $this->hashIV = $hashIV;
    }

    /**
     * 將內容加上 CheckMacValue。
     *
     * @param array<string, mixed> $payload 原始資料
     * @return array<string, mixed> 加上 CheckMacValue 的資料
     */
    public function encodePayload(array $payload): array
    {
        // 移除既有的 CheckMacValue
        unset($payload['CheckMacValue']);

        // 計算 CheckMacValue
        $payload['CheckMacValue'] = $this->generateCheckMacValue($payload);

        return $payload;
    }

    /**
     * 驗證回傳資料的 CheckMacValue。
     *
     * @param array<string, mixed> $responseData 回傳資料
     * @return bool
     */
    public function verifyResponse(array $responseData): bool
    {
        if (!isset($responseData['CheckMacValue'])) {
            return false;
        }

        $receivedCheckMac = $responseData['CheckMacValue'];
        unset($responseData['CheckMacValue']);

        $calculatedCheckMac = $this->generateCheckMacValue($responseData);

        return strtoupper($receivedCheckMac) === strtoupper($calculatedCheckMac);
    }

    /**
     * 驗證並拋出例外。
     *
     * @param array<string, mixed> $responseData 回傳資料
     * @return array<string, mixed> 已驗證的資料
     * @throws LogisticsException 當驗證失敗時
     */
    public function verifyOrFail(array $responseData): array
    {
        if (!$this->verifyResponse($responseData)) {
            throw LogisticsException::checkMacValueFailed();
        }

        return $responseData;
    }

    /**
     * 產生 CheckMacValue。
     *
     * 物流 API 使用 MD5 加密機制。
     *
     * @param array<string, mixed> $data 資料
     * @return string
     */
    public function generateCheckMacValue(array $data): string
    {
        // 1. 依照參數名稱字母排序（A-Z）
        ksort($data, SORT_STRING | SORT_FLAG_CASE);

        // 2. 組成查詢字串
        $queryString = urldecode(http_build_query($data));

        // 3. 加上 HashKey 和 HashIV
        $raw = "HashKey={$this->hashKey}&{$queryString}&HashIV={$this->hashIV}";

        // 4. URL encode 並轉小寫
        $encoded = strtolower(urlencode($raw));

        // 5. 處理 .NET 相容的 URL encode 差異
        $encoded = $this->dotNetUrlEncode($encoded);

        // 6. 計算 MD5 雜湊值（物流 API 使用 MD5）
        return strtoupper(md5($encoded));
    }

    /**
     * 處理與 .NET 相容的 URL encode。
     *
     * PHP 與 .NET 的 URL encode 結果有些許差異，需要做轉換。
     *
     * @param string $string 已 URL encode 的字串
     * @return string
     */
    private function dotNetUrlEncode(string $string): string
    {
        // 這些字元在 .NET 的 urlencode 結果與 PHP 不同
        $search = [
            '%2d', // -
            '%5f', // _
            '%2e', // .
            '%21', // !
            '%2a', // *
            '%28', // (
            '%29', // )
            '%20', // space
        ];

        $replace = [
            '-',
            '_',
            '.',
            '!',
            '*',
            '(',
            ')',
            '+',
        ];

        return str_replace($search, $replace, $string);
    }
}
