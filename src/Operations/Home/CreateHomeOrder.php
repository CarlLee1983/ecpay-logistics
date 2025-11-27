<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Operations\Home;

use CarlLee\EcPayLogistics\Content;
use CarlLee\EcPayLogistics\Exceptions\LogisticsException;
use CarlLee\EcPayLogistics\Parameter\Distance;
use CarlLee\EcPayLogistics\Parameter\IsCollection;
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;
use CarlLee\EcPayLogistics\Parameter\LogisticsType;
use CarlLee\EcPayLogistics\Parameter\ScheduledDeliveryTime;
use CarlLee\EcPayLogistics\Parameter\ScheduledPickupTime;
use CarlLee\EcPayLogistics\Parameter\Specification;
use CarlLee\EcPayLogistics\Parameter\Temperature;

/**
 * 建立宅配物流訂單。
 *
 * 支援黑貓宅急便、中華郵政。
 */
class CreateHomeOrder extends Content
{
    /**
     * API 請求路徑。
     */
    protected string $requestPath = '/Express/Create';

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

        $this->content['MerchantTradeDate'] = date('Y/m/d H:i:s');
        $this->content['LogisticsType'] = LogisticsType::HOME->value;
        $this->content['LogisticsSubType'] = LogisticsSubType::TCAT->value;
        $this->content['GoodsAmount'] = 0;
        $this->content['IsCollection'] = IsCollection::NO->value;
        $this->content['Temperature'] = Temperature::ROOM->value;
        $this->content['Distance'] = Distance::SAME->value;
        $this->content['Specification'] = Specification::SIZE_60->value;
        $this->content['ScheduledPickupTime'] = ScheduledPickupTime::UNLIMITED->value;
        $this->content['ScheduledDeliveryTime'] = ScheduledDeliveryTime::UNLIMITED->value;
    }

    /**
     * 設定物流子類型。
     *
     * @param LogisticsSubType $subType 物流子類型
     * @return static
     */
    public function setLogisticsSubType(LogisticsSubType $subType): static
    {
        if (!$subType->isHome()) {
            throw LogisticsException::invalid('LogisticsSubType', '必須為宅配類型');
        }

        $this->content['LogisticsSubType'] = $subType->value;
        $this->content['LogisticsType'] = LogisticsType::HOME->value;

        return $this;
    }

    /**
     * 設定是否代收貨款。
     *
     * @param IsCollection|string $isCollection 是否代收貨款
     * @return static
     */
    public function setIsCollection(IsCollection|string $isCollection): static
    {
        if ($isCollection instanceof IsCollection) {
            $this->content['IsCollection'] = $isCollection->value;
        } else {
            $this->content['IsCollection'] = $isCollection;
        }

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
     * 設定代收金額。
     *
     * @param int $amount 金額
     * @return static
     */
    public function setCollectionAmount(int $amount): static
    {
        if ($amount < 0) {
            throw LogisticsException::invalid('CollectionAmount', '金額不可為負數');
        }

        $this->content['CollectionAmount'] = $amount;

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
     * 設定配送距離。
     *
     * @param Distance $distance 配送距離
     * @return static
     */
    public function setDistance(Distance $distance): static
    {
        $this->content['Distance'] = $distance->value;

        return $this;
    }

    /**
     * 設定包裹規格。
     *
     * @param Specification $specification 包裹規格
     * @return static
     */
    public function setSpecification(Specification $specification): static
    {
        $this->content['Specification'] = $specification->value;

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
     * 設定預約配送時段。
     *
     * @param ScheduledDeliveryTime $time 配送時段
     * @return static
     */
    public function setScheduledDeliveryTime(ScheduledDeliveryTime $time): static
    {
        $this->content['ScheduledDeliveryTime'] = $time->value;

        return $this;
    }

    /**
     * 設定預約配送日期。
     *
     * @param \DateTimeInterface|string $date 日期
     * @return static
     */
    public function setScheduledDeliveryDate(\DateTimeInterface|string $date): static
    {
        if ($date instanceof \DateTimeInterface) {
            $date = $date->format('Y/m/d');
        }

        $this->content['ScheduledDeliveryDate'] = $date;

        return $this;
    }

    /**
     * 設定包裹數量。
     *
     * @param int $count 數量
     * @return static
     */
    public function setPackageCount(int $count): static
    {
        $this->content['PackageCount'] = $count;

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

        if (empty($this->content['MerchantTradeNo'])) {
            throw LogisticsException::required('MerchantTradeNo');
        }

        if (empty($this->content['MerchantTradeDate'])) {
            throw LogisticsException::required('MerchantTradeDate');
        }

        if (empty($this->content['LogisticsSubType'])) {
            throw LogisticsException::required('LogisticsSubType');
        }

        if (empty($this->content['GoodsName'])) {
            throw LogisticsException::required('GoodsName');
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
     * 使用黑貓宅急便。
     *
     * @return static
     */
    public function useTcat(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::TCAT);
    }

    /**
     * 使用中華郵政。
     *
     * @return static
     */
    public function usePost(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::POST);
    }

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

    /**
     * 設定同縣市配送。
     *
     * @return static
     */
    public function sameCity(): static
    {
        return $this->setDistance(Distance::SAME);
    }

    /**
     * 設定外縣市配送。
     *
     * @return static
     */
    public function otherCity(): static
    {
        return $this->setDistance(Distance::OTHER);
    }

    /**
     * 設定離島配送。
     *
     * @return static
     */
    public function island(): static
    {
        return $this->setDistance(Distance::ISLAND);
    }

    /**
     * 啟用代收貨款。
     *
     * @param int $amount 代收金額（不填則使用 GoodsAmount）
     * @return static
     */
    public function withCollection(int $amount = 0): static
    {
        $this->setIsCollection(IsCollection::YES);

        if ($amount > 0) {
            $this->setCollectionAmount($amount);
        }

        return $this;
    }

    /**
     * 停用代收貨款。
     *
     * @return static
     */
    public function withoutCollection(): static
    {
        return $this->setIsCollection(IsCollection::NO);
    }
}
