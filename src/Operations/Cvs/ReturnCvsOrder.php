<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Operations\Cvs;

use CarlLee\EcPayLogistics\Content;
use CarlLee\EcPayLogistics\Exceptions\LogisticsException;
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;

/**
 * B2C 超商逆物流訂單。
 *
 * 適用於：7-ELEVEN B2C、全家 B2C、萊爾富 B2C。
 */
class ReturnCvsOrder extends Content
{
    /**
     * API 請求路徑（依據超商類型不同）。
     *
     * - 7-ELEVEN: /Express/ReturnUniMartCVS
     * - 全家: /Express/ReturnCVS
     * - 萊爾富: /Express/ReturnHiLifeCVS
     */
    protected string $requestPath = '/Express/ReturnCVS';

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

        $this->content['GoodsAmount'] = 0;
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
     * 設定物流子類型並調整 API 路徑。
     *
     * @param LogisticsSubType $subType 物流子類型
     * @return static
     */
    public function setLogisticsSubType(LogisticsSubType $subType): static
    {
        if (!$subType->isB2C()) {
            throw LogisticsException::invalid('LogisticsSubType', '逆物流僅適用於 B2C');
        }

        $this->content['LogisticsSubType'] = $subType->value;

        // 根據超商類型設定不同的 API 路徑
        $this->requestPath = match ($subType) {
            LogisticsSubType::UNIMART => '/Express/ReturnUniMartCVS',
            LogisticsSubType::HILIFE => '/Express/ReturnHiLifeCVS',
            default => '/Express/ReturnCVS',
        };

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
     * 設定收件人姓名（萊爾富專用）。
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
     * 設定收件人電話（萊爾富專用）。
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
     * 設定收件人手機（萊爾富專用）。
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
     * 設定收件人 Email（萊爾富專用）。
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

        if (empty($this->content['LogisticsSubType'])) {
            throw LogisticsException::required('LogisticsSubType');
        }

        if (empty($this->content['ServerReplyURL'])) {
            throw LogisticsException::required('ServerReplyURL');
        }
    }

    // ========== 便利方法 ==========

    /**
     * 使用 7-ELEVEN B2C 逆物流。
     *
     * @return static
     */
    public function useUnimartB2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::UNIMART);
    }

    /**
     * 使用全家 B2C 逆物流。
     *
     * @return static
     */
    public function useFamiB2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::FAMI);
    }

    /**
     * 使用萊爾富 B2C 逆物流。
     *
     * @return static
     */
    public function useHilifeB2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::HILIFE);
    }
}
