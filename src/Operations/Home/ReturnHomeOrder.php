<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Operations\Home;

use CarlLee\EcPayLogistics\Content;
use CarlLee\EcPayLogistics\Exceptions\LogisticsException;
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;
use CarlLee\EcPayLogistics\Parameter\ScheduledPickupTime;
use CarlLee\EcPayLogistics\Parameter\Temperature;

/**
 * 宅配逆物流訂單。
 *
 * 僅適用於黑貓宅急便（中華郵政不提供逆物流）。
 */
class ReturnHomeOrder extends Content
{
    /**
     * API 請求路徑。
     */
    protected string $requestPath = '/Express/ReturnHome';

    /**
     * 商品名稱最大長度。
     */
    public const int GOODS_NAME_MAX_LENGTH = 50;

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function initContent(): void
    {
        parent::initContent();

        $this->content['LogisticsSubType'] = LogisticsSubType::TCAT->value;
        $this->content['GoodsAmount'] = 0;
        $this->content['Temperature'] = Temperature::ROOM->value;
        $this->content['ScheduledPickupTime'] = ScheduledPickupTime::UNLIMITED->value;
    }

    /**
     * 設定綠界物流交易編號。
     *
     * @param string $logisticsId 物流交易編號
     * @return static
     */
    public function setAllPayLogisticsID(string $logisticsId): static
    {
        $this->content['AllPayLogisticsID'] = $logisticsId;

        return $this;
    }

    /**
     * 設定物流子類型。
     *
     * @param LogisticsSubType $subType 物流子類型
     * @return static
     */
    public function setLogisticsSubType(LogisticsSubType $subType): static
    {
        if ($subType !== LogisticsSubType::TCAT) {
            throw LogisticsException::invalid('LogisticsSubType', '宅配逆物流僅適用於黑貓宅急便');
        }

        $this->content['LogisticsSubType'] = $subType->value;

        return $this;
    }

    /**
     * 設定商品金額。
     *
     * @param int $amount 金額
     * @return static
     */
    public function setGoodsAmount(int $amount): static
    {
        if ($amount < 0) {
            throw LogisticsException::invalid('GoodsAmount', '金額不可為負數');
        }

        $this->content['GoodsAmount'] = $amount;

        return $this;
    }

    /**
     * 設定商品名稱。
     *
     * @param string $name 商品名稱
     * @return static
     */
    public function setGoodsName(string $name): static
    {
        if (mb_strlen($name) > self::GOODS_NAME_MAX_LENGTH) {
            throw LogisticsException::tooLong('GoodsName', self::GOODS_NAME_MAX_LENGTH);
        }

        $this->content['GoodsName'] = $name;

        return $this;
    }

    /**
     * 設定溫層。
     *
     * @param Temperature $temperature 溫層
     * @return static
     */
    public function setTemperature(Temperature $temperature): static
    {
        $this->content['Temperature'] = $temperature->value;

        return $this;
    }

    /**
     * 設定預約取件時段。
     *
     * @param ScheduledPickupTime $time 取件時段
     * @return static
     */
    public function setScheduledPickupTime(ScheduledPickupTime $time): static
    {
        $this->content['ScheduledPickupTime'] = $time->value;

        return $this;
    }

    /**
     * 設定服務類型。
     *
     * @param string $serviceType 服務類型
     * @return static
     */
    public function setServiceType(string $serviceType): static
    {
        $this->content['ServiceType'] = $serviceType;

        return $this;
    }

    /**
     * 設定寄件人姓名。
     *
     * @param string $name 姓名
     * @return static
     */
    public function setSenderName(string $name): static
    {
        $this->content['SenderName'] = $name;

        return $this;
    }

    /**
     * 設定寄件人電話。
     *
     * @param string $phone 電話
     * @return static
     */
    public function setSenderPhone(string $phone): static
    {
        $this->content['SenderPhone'] = $phone;

        return $this;
    }

    /**
     * 設定寄件人手機。
     *
     * @param string $cellPhone 手機
     * @return static
     */
    public function setSenderCellPhone(string $cellPhone): static
    {
        $this->content['SenderCellPhone'] = $cellPhone;

        return $this;
    }

    /**
     * 設定寄件人郵遞區號。
     *
     * @param string $zipCode 郵遞區號
     * @return static
     */
    public function setSenderZipCode(string $zipCode): static
    {
        $this->content['SenderZipCode'] = $zipCode;

        return $this;
    }

    /**
     * 設定寄件人地址。
     *
     * @param string $address 地址
     * @return static
     */
    public function setSenderAddress(string $address): static
    {
        $this->content['SenderAddress'] = $address;

        return $this;
    }

    /**
     * 設定收件人姓名。
     *
     * @param string $name 姓名
     * @return static
     */
    public function setReceiverName(string $name): static
    {
        $this->content['ReceiverName'] = $name;

        return $this;
    }

    /**
     * 設定收件人電話。
     *
     * @param string $phone 電話
     * @return static
     */
    public function setReceiverPhone(string $phone): static
    {
        $this->content['ReceiverPhone'] = $phone;

        return $this;
    }

    /**
     * 設定收件人手機。
     *
     * @param string $cellPhone 手機
     * @return static
     */
    public function setReceiverCellPhone(string $cellPhone): static
    {
        $this->content['ReceiverCellPhone'] = $cellPhone;

        return $this;
    }

    /**
     * 設定收件人郵遞區號。
     *
     * @param string $zipCode 郵遞區號
     * @return static
     */
    public function setReceiverZipCode(string $zipCode): static
    {
        $this->content['ReceiverZipCode'] = $zipCode;

        return $this;
    }

    /**
     * 設定收件人地址。
     *
     * @param string $address 地址
     * @return static
     */
    public function setReceiverAddress(string $address): static
    {
        $this->content['ReceiverAddress'] = $address;

        return $this;
    }

    /**
     * 設定收件人 Email。
     *
     * @param string $email Email
     * @return static
     */
    public function setReceiverEmail(string $email): static
    {
        $this->content['ReceiverEmail'] = $email;

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function validation(): void
    {
        $this->validateBaseParams();

        if (empty($this->content['AllPayLogisticsID'])) {
            throw LogisticsException::required('AllPayLogisticsID');
        }

        if (empty($this->content['SenderName'])) {
            throw LogisticsException::required('SenderName');
        }

        if (empty($this->content['SenderPhone']) && empty($this->content['SenderCellPhone'])) {
            throw LogisticsException::required('SenderPhone 或 SenderCellPhone');
        }

        if (empty($this->content['SenderZipCode'])) {
            throw LogisticsException::required('SenderZipCode');
        }

        if (empty($this->content['SenderAddress'])) {
            throw LogisticsException::required('SenderAddress');
        }

        if (empty($this->content['ReceiverName'])) {
            throw LogisticsException::required('ReceiverName');
        }

        if (empty($this->content['ReceiverPhone']) && empty($this->content['ReceiverCellPhone'])) {
            throw LogisticsException::required('ReceiverPhone 或 ReceiverCellPhone');
        }

        if (empty($this->content['ReceiverZipCode'])) {
            throw LogisticsException::required('ReceiverZipCode');
        }

        if (empty($this->content['ReceiverAddress'])) {
            throw LogisticsException::required('ReceiverAddress');
        }

        if (empty($this->content['ServerReplyURL'])) {
            throw LogisticsException::required('ServerReplyURL');
        }
    }

    // ========== 便利方法 ==========

    /**
     * 設定常溫配送。
     *
     * @return static
     */
    public function roomTemperature(): static
    {
        return $this->setTemperature(Temperature::ROOM);
    }

    /**
     * 設定冷藏配送。
     *
     * @return static
     */
    public function refrigeration(): static
    {
        return $this->setTemperature(Temperature::REFRIGERATION);
    }

    /**
     * 設定冷凍配送。
     *
     * @return static
     */
    public function freeze(): static
    {
        return $this->setTemperature(Temperature::FREEZE);
    }
}
