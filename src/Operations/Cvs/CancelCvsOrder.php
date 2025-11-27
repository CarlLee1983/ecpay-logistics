<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Operations\Cvs;

use CarlLee\EcPayLogistics\Content;
use CarlLee\EcPayLogistics\Exceptions\LogisticsException;

/**
 * 取消超商物流訂單。
 *
 * 僅適用於 C2C 7-ELEVEN。
 */
class CancelCvsOrder extends Content
{
    /**
     * API 請求路徑。
     */
    protected string $requestPath = '/Express/CancelC2COrder';

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
     * 設定 CVS 出貨單號。
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
     * 設定 CVS 驗證碼。
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
     * @inheritDoc
     */
    #[\Override]
    protected function validation(): void
    {
        $this->validateBaseParams();

        if (empty($this->content['AllPayLogisticsID'])) {
            throw LogisticsException::required('AllPayLogisticsID');
        }

        if (empty($this->content['CVSPaymentNo'])) {
            throw LogisticsException::required('CVSPaymentNo');
        }

        if (empty($this->content['CVSValidationNo'])) {
            throw LogisticsException::required('CVSValidationNo');
        }
    }
}
