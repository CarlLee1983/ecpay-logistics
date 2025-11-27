<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Operations\Cvs;

use CarlLee\EcPayLogistics\Content;
use CarlLee\EcPayLogistics\Exceptions\LogisticsException;
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;

/**
 * 異動超商物流訂單。
 *
 * 適用於：
 * - B2C: 7-ELEVEN、萊爾富
 * - C2C: 7-ELEVEN、全家、OK、萊爾富
 */
class UpdateCvsOrder extends Content
{
    /**
     * API 請求路徑。
     */
    protected string $requestPath = '/Helper/UpdateShipmentInfo';

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function initContent(): void
    {
        parent::initContent();
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
        if (!$subType->isCvs()) {
            throw LogisticsException::invalid('LogisticsSubType', '必須為超商類型');
        }

        $this->content['LogisticsSubType'] = $subType->value;

        return $this;
    }

    /**
     * 設定出貨日期。
     *
     * @param \DateTimeInterface|string $date 出貨日期
     * @return static
     */
    public function setShipmentDate(\DateTimeInterface|string $date): static
    {
        if ($date instanceof \DateTimeInterface) {
            $date = $date->format('Y/m/d');
        }

        $this->content['ShipmentDate'] = $date;

        return $this;
    }

    /**
     * 設定物流狀態（B2C 專用）。
     *
     * @param string $status 物流狀態
     * @return static
     */
    public function setLogisticsStatus(string $status): static
    {
        $this->content['LogisticsStatus'] = $status;

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
     * 設定商品名稱。
     *
     * @param string $name 商品名稱
     * @return static
     */
    public function setGoodsName(string $name): static
    {
        $this->content['GoodsName'] = $name;

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
        $this->content['GoodsAmount'] = $amount;

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
     * 設定 CVS 出貨單號（7-ELEVEN C2C 專用）。
     *
     * @param string $paymentNo 出貨單號
     * @return static
     */
    public function setCVSPaymentNo(string $paymentNo): static
    {
        $this->content['CVSPaymentNo'] = $paymentNo;

        return $this;
    }

    /**
     * 設定 CVS 驗證碼（7-ELEVEN C2C 專用）。
     *
     * @param string $validationNo 驗證碼
     * @return static
     */
    public function setCVSValidationNo(string $validationNo): static
    {
        $this->content['CVSValidationNo'] = $validationNo;

        return $this;
    }

    /**
     * 設定門市代號（7-ELEVEN C2C 專用）。
     *
     * @param string $storeId 門市代號
     * @return static
     */
    public function setStoreID(string $storeId): static
    {
        $this->content['StoreID'] = $storeId;

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

        if (empty($this->content['LogisticsSubType'])) {
            throw LogisticsException::required('LogisticsSubType');
        }
    }
}
