<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Notifications;

use CarlLee\EcPayLogistics\Contracts\NotifyHandlerInterface;
use CarlLee\EcPayLogistics\Exceptions\LogisticsException;
use CarlLee\EcPayLogistics\Infrastructure\CheckMacEncoder;

/**
 * 物流狀態通知處理。
 *
 * 用於處理綠界回傳的物流狀態通知（ServerReplyURL）。
 */
class LogisticsNotify implements NotifyHandlerInterface
{
    /**
     * CheckMac 編碼器。
     */
    private readonly CheckMacEncoder $encoder;

    /**
     * 通知資料。
     *
     * @var array<string, mixed>
     */
    private array $data = [];

    /**
     * 是否已驗證。
     */
    private bool $verified = false;

    /**
     * 建立通知處理器。
     *
     * @param string $hashKey HashKey
     * @param string $hashIV HashIV
     */
    public function __construct(string $hashKey, string $hashIV)
    {
        $this->encoder = new CheckMacEncoder($hashKey, $hashIV);
    }

    /**
     * @inheritDoc
     */
    public function verify(array $data): bool
    {
        $this->data = $data;
        $this->verified = $this->encoder->verifyResponse($data);

        return $this->verified;
    }

    /**
     * 驗證並拋出例外。
     *
     * @param array<string, mixed> $data 通知資料
     * @return static
     * @throws LogisticsException 當驗證失敗時
     */
    public function verifyOrFail(array $data): static
    {
        if (!$this->verify($data)) {
            throw LogisticsException::checkMacValueFailed();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function isSuccess(): bool
    {
        // 物流狀態代碼為數字，需要判斷是否為成功的狀態
        // 常見成功狀態：300（已出貨）、2030（已取貨）等
        $successCodes = ['300', '2030', '2063', '2067', '2073', '3018'];

        return in_array($this->getRtnCode(), $successCodes, true);
    }

    /**
     * @inheritDoc
     */
    public function getRtnCode(): string
    {
        return (string) ($this->data['RtnCode'] ?? '');
    }

    /**
     * @inheritDoc
     */
    public function getRtnMsg(): string
    {
        return (string) ($this->data['RtnMsg'] ?? '');
    }

    /**
     * 取得綠界物流交易編號。
     *
     * @return string
     */
    public function getAllPayLogisticsID(): string
    {
        return (string) ($this->data['AllPayLogisticsID'] ?? '');
    }

    /**
     * 取得特店交易編號。
     *
     * @return string
     */
    public function getMerchantTradeNo(): string
    {
        return (string) ($this->data['MerchantTradeNo'] ?? '');
    }

    /**
     * 取得特店編號。
     *
     * @return string
     */
    public function getMerchantID(): string
    {
        return (string) ($this->data['MerchantID'] ?? '');
    }

    /**
     * 取得物流類型。
     *
     * @return string
     */
    public function getLogisticsType(): string
    {
        return (string) ($this->data['LogisticsType'] ?? '');
    }

    /**
     * 取得物流子類型。
     *
     * @return string
     */
    public function getLogisticsSubType(): string
    {
        return (string) ($this->data['LogisticsSubType'] ?? '');
    }

    /**
     * 取得商品金額。
     *
     * @return int
     */
    public function getGoodsAmount(): int
    {
        return (int) ($this->data['GoodsAmount'] ?? 0);
    }

    /**
     * 取得更新時間。
     *
     * @return string
     */
    public function getUpdateStatusDate(): string
    {
        return (string) ($this->data['UpdateStatusDate'] ?? '');
    }

    /**
     * 取得收件人姓名。
     *
     * @return string
     */
    public function getReceiverName(): string
    {
        return (string) ($this->data['ReceiverName'] ?? '');
    }

    /**
     * 取得收件人電話。
     *
     * @return string
     */
    public function getReceiverPhone(): string
    {
        return (string) ($this->data['ReceiverPhone'] ?? '');
    }

    /**
     * 取得收件人手機。
     *
     * @return string
     */
    public function getReceiverCellPhone(): string
    {
        return (string) ($this->data['ReceiverCellPhone'] ?? '');
    }

    /**
     * 取得收件人 Email。
     *
     * @return string
     */
    public function getReceiverEmail(): string
    {
        return (string) ($this->data['ReceiverEmail'] ?? '');
    }

    /**
     * 取得收件人地址。
     *
     * @return string
     */
    public function getReceiverAddress(): string
    {
        return (string) ($this->data['ReceiverAddress'] ?? '');
    }

    /**
     * 取得 CVS 出貨單號。
     *
     * @return string
     */
    public function getCVSPaymentNo(): string
    {
        return (string) ($this->data['CVSPaymentNo'] ?? '');
    }

    /**
     * 取得 CVS 驗證碼。
     *
     * @return string
     */
    public function getCVSValidationNo(): string
    {
        return (string) ($this->data['CVSValidationNo'] ?? '');
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
     * 是否已驗證。
     *
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->verified;
    }

    /**
     * @inheritDoc
     */
    public function getSuccessResponse(): string
    {
        return '1|OK';
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
}
