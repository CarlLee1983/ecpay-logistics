<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics;

use CarlLee\EcPayLogistics\Exceptions\LogisticsException;
use CarlLee\EcPayLogistics\Infrastructure\CheckMacEncoder;

/**
 * API 回應封裝。
 */
class Response
{
    /**
     * 原始回應內容。
     */
    private string $rawBody;

    /**
     * 解析後的資料。
     *
     * @var array<string, mixed>
     */
    private array $data = [];

    /**
     * CheckMac 編碼器。
     */
    private ?CheckMacEncoder $encoder;

    /**
     * 是否已驗證。
     */
    private bool $verified = false;

    /**
     * 建立回應物件。
     *
     * @param string $body 原始回應內容
     * @param CheckMacEncoder|null $encoder CheckMac 編碼器
     */
    public function __construct(string $body, ?CheckMacEncoder $encoder = null)
    {
        $this->rawBody = $body;
        $this->encoder = $encoder;
        $this->parseBody();
    }

    /**
     * 解析回應內容。
     */
    private function parseBody(): void
    {
        // 嘗試解析為 URL 編碼格式
        parse_str($this->rawBody, $this->data);

        // 如果解析失敗，嘗試 JSON
        if (empty($this->data)) {
            $decoded = json_decode($this->rawBody, true);
            if (is_array($decoded)) {
                $this->data = $decoded;
            }
        }
    }

    /**
     * 驗證 CheckMacValue。
     *
     * @return bool
     */
    public function verify(): bool
    {
        if ($this->encoder === null) {
            return false;
        }

        $this->verified = $this->encoder->verifyResponse($this->data);

        return $this->verified;
    }

    /**
     * 驗證並拋出例外。
     *
     * @return static
     * @throws LogisticsException 當驗證失敗時
     */
    public function verifyOrFail(): static
    {
        if ($this->encoder !== null) {
            $this->encoder->verifyOrFail($this->data);
            $this->verified = true;
        }

        return $this;
    }

    /**
     * 是否成功。
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        $rtnCode = $this->getRtnCode();

        // 物流 API 成功代碼通常為 1 或 300
        return $rtnCode === '1' || $rtnCode === '300';
    }

    /**
     * 取得回傳代碼。
     *
     * @return string
     */
    public function getRtnCode(): string
    {
        return (string) ($this->data['RtnCode'] ?? '');
    }

    /**
     * 取得回傳訊息。
     *
     * @return string
     */
    public function getRtnMsg(): string
    {
        return (string) ($this->data['RtnMsg'] ?? $this->data['RtnMsgE'] ?? '');
    }

    /**
     * 取得綠界物流交易編號。
     *
     * @return string
     */
    public function getAllPayLogisticsID(): string
    {
        return (string) ($this->data['AllPayLogisticsID'] ?? $this->data['1|AllPayLogisticsID'] ?? '');
    }

    /**
     * 取得貨運單號。
     *
     * @return string
     */
    public function getBookingNote(): string
    {
        return (string) ($this->data['BookingNote'] ?? '');
    }

    /**
     * 取得超商店舖代號。
     *
     * @return string
     */
    public function getCVSStoreID(): string
    {
        return (string) ($this->data['CVSStoreID'] ?? '');
    }

    /**
     * 取得超商店舖名稱。
     *
     * @return string
     */
    public function getCVSStoreName(): string
    {
        return (string) ($this->data['CVSStoreName'] ?? '');
    }

    /**
     * 取得超商店舖地址。
     *
     * @return string
     */
    public function getCVSAddress(): string
    {
        return (string) ($this->data['CVSAddress'] ?? '');
    }

    /**
     * 取得超商店舖電話。
     *
     * @return string
     */
    public function getCVSTelephone(): string
    {
        return (string) ($this->data['CVSTelephone'] ?? '');
    }

    /**
     * 取得超商出貨編號。
     *
     * @return string
     */
    public function getCVSPaymentNo(): string
    {
        return (string) ($this->data['CVSPaymentNo'] ?? '');
    }

    /**
     * 取得超商驗證碼。
     *
     * @return string
     */
    public function getCVSValidationNo(): string
    {
        return (string) ($this->data['CVSValidationNo'] ?? '');
    }

    /**
     * 取得列印託運單網址。
     *
     * @return string
     */
    public function getPrintUrl(): string
    {
        // 處理不同 API 回傳的列印網址
        return (string) ($this->data['PrintURL'] ?? '');
    }

    /**
     * 取得原始資料。
     *
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * 取得特定欄位。
     *
     * @param string $key 欄位名稱
     * @param mixed $default 預設值
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * 取得原始回應內容。
     *
     * @return string
     */
    public function getRawBody(): string
    {
        return $this->rawBody;
    }

    /**
     * 是否已驗證。
     *
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->verified;
    }

    /**
     * 轉換為陣列。
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
