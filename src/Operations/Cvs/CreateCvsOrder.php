<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Operations\Cvs;

use CarlLee\EcPayLogistics\Content;
use CarlLee\EcPayLogistics\Exceptions\LogisticsException;
use CarlLee\EcPayLogistics\Parameter\IsCollection;
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;
use CarlLee\EcPayLogistics\Parameter\LogisticsType;

/**
 * 建立超商物流訂單。
 *
 * 支援 C2C 及 B2C 超商取貨訂單。
 */
class CreateCvsOrder extends Content
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
     * 寄件人姓名最大長度。
     */
    public const int SENDER_NAME_MAX_LENGTH = 10;

    /**
     * 收件人姓名最大長度。
     */
    public const int RECEIVER_NAME_MAX_LENGTH = 10;

    /**
     * @inheritDoc
     */
    protected function initContent(): void
    {
        parent::initContent();

        $this->content['MerchantTradeDate'] = date('Y/m/d H:i:s');
        $this->content['LogisticsType'] = LogisticsType::CVS->value;
        $this->content['LogisticsSubType'] = LogisticsSubType::UNIMART_C2C->value;
        $this->content['GoodsAmount'] = 0;
        $this->content['IsCollection'] = IsCollection::NO->value;
    }

    /**
     * 設定物流子類型。
     *
     * @param LogisticsSubType $subType 物流子類型
     * @return static
     */
    public function setLogisticsSubType(LogisticsSubType $subType): static
    {
        if (!$subType->isCvs()) {
            throw LogisticsException::invalid('LogisticsSubType', '必須為超商類型');
        }

        $this->content['LogisticsSubType'] = $subType->value;
        $this->content['LogisticsType'] = LogisticsType::CVS->value;

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
     * 設定服務類型（B2C 專用）。
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
     * 設定寄件人姓名。
     *
     * @param string $name 姓名
     * @return static
     */
    public function setSenderName(string $name): static
    {
        if (mb_strlen($name) > self::SENDER_NAME_MAX_LENGTH) {
            throw LogisticsException::tooLong('SenderName', self::SENDER_NAME_MAX_LENGTH);
        }

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
     * 設定寄件人郵遞區號（B2C 專用）。
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
     * 設定寄件人地址（B2C 專用）。
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
        if (mb_strlen($name) > self::RECEIVER_NAME_MAX_LENGTH) {
            throw LogisticsException::tooLong('ReceiverName', self::RECEIVER_NAME_MAX_LENGTH);
        }

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
     * 設定收件門市代號。
     *
     * @param string $storeId 門市代號
     * @return static
     */
    public function setReceiverStoreID(string $storeId): static
    {
        $this->content['ReceiverStoreID'] = $storeId;

        return $this;
    }

    /**
     * 設定退貨門市代號（B2C 專用）。
     *
     * @param string $storeId 門市代號
     * @return static
     */
    public function setReturnStoreID(string $storeId): static
    {
        $this->content['ReturnStoreID'] = $storeId;

        return $this;
    }

    /**
     * 設定綠界物流交易編號（更新訂單時使用）。
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
     * @inheritDoc
     */
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

        // 寄件人電話或手機至少填一項
        if (empty($this->content['SenderPhone']) && empty($this->content['SenderCellPhone'])) {
            throw LogisticsException::required('SenderPhone 或 SenderCellPhone');
        }

        if (empty($this->content['ReceiverName'])) {
            throw LogisticsException::required('ReceiverName');
        }

        // 收件人電話或手機至少填一項
        if (empty($this->content['ReceiverPhone']) && empty($this->content['ReceiverCellPhone'])) {
            throw LogisticsException::required('ReceiverPhone 或 ReceiverCellPhone');
        }

        if (empty($this->content['ReceiverStoreID'])) {
            throw LogisticsException::required('ReceiverStoreID');
        }

        if (empty($this->content['ServerReplyURL'])) {
            throw LogisticsException::required('ServerReplyURL');
        }
    }

    // ========== 便利方法 ==========

    /**
     * 使用 7-ELEVEN C2C。
     *
     * @return static
     */
    public function useUnimartC2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::UNIMART_C2C);
    }

    /**
     * 使用全家 C2C。
     *
     * @return static
     */
    public function useFamiC2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::FAMI_C2C);
    }

    /**
     * 使用萊爾富 C2C。
     *
     * @return static
     */
    public function useHilifeC2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::HILIFE_C2C);
    }

    /**
     * 使用 OK超商 C2C。
     *
     * @return static
     */
    public function useOkmartC2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::OKMART_C2C);
    }

    /**
     * 使用 7-ELEVEN B2C。
     *
     * @return static
     */
    public function useUnimartB2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::UNIMART);
    }

    /**
     * 使用全家 B2C。
     *
     * @return static
     */
    public function useFamiB2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::FAMI);
    }

    /**
     * 使用萊爾富 B2C。
     *
     * @return static
     */
    public function useHilifeB2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::HILIFE);
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
