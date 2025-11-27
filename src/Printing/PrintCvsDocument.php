<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Printing;

use CarlLee\EcPayLogistics\Content;
use CarlLee\EcPayLogistics\Exceptions\LogisticsException;
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;

/**
 * 列印 C2C 超商託運單。
 *
 * 適用於 C2C 超商（7-ELEVEN、全家、萊爾富、OK超商）。
 */
class PrintCvsDocument extends Content
{
    /**
     * API 請求路徑（依據超商類型不同）。
     */
    protected string $requestPath = '/Express/PrintUniMartC2COrderInfo';

    /**
     * 物流交易編號列表。
     *
     * @var array<int, string>
     */
    protected array $allPayLogisticsIDs = [];

    /**
     * CVS 出貨單號列表（7-ELEVEN C2C 專用）。
     *
     * @var array<int, string>
     */
    protected array $cvsPaymentNos = [];

    /**
     * CVS 驗證碼列表（7-ELEVEN C2C 專用）。
     *
     * @var array<int, string>
     */
    protected array $cvsValidationNos = [];

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function initContent(): void
    {
        parent::initContent();
    }

    /**
     * 設定物流子類型並調整 API 路徑。
     *
     * @param LogisticsSubType $subType 物流子類型
     * @return static
     */
    public function setLogisticsSubType(LogisticsSubType $subType): static
    {
        if (!$subType->isC2C()) {
            throw LogisticsException::invalid('LogisticsSubType', '請使用 PrintTradeDocument 列印 B2C/宅配託運單');
        }

        $this->content['LogisticsSubType'] = $subType->value;

        // 根據超商類型設定不同的 API 路徑
        $this->requestPath = match ($subType) {
            LogisticsSubType::UNIMART_C2C => '/Express/PrintUniMartC2COrderInfo',
            LogisticsSubType::FAMI_C2C => '/Express/PrintFAMIC2COrderInfo',
            LogisticsSubType::HILIFE_C2C => '/Express/PrintHILIFEC2COrderInfo',
            LogisticsSubType::OKMART_C2C => '/Express/PrintOKMARTC2COrderInfo',
            default => throw LogisticsException::unsupportedLogisticsType($subType->value),
        };

        return $this;
    }

    /**
     * 設定綠界物流交易編號（單筆）。
     *
     * @param string $logisticsId 物流交易編號
     * @return static
     */
    public function setAllPayLogisticsID(string $logisticsId): static
    {
        $this->allPayLogisticsIDs = [$logisticsId];
        $this->content['AllPayLogisticsID'] = $logisticsId;

        return $this;
    }

    /**
     * 設定綠界物流交易編號（批次）。
     *
     * @param array<int, string> $logisticsIds 物流交易編號陣列
     * @return static
     */
    public function setAllPayLogisticsIDs(array $logisticsIds): static
    {
        $this->allPayLogisticsIDs = array_values($logisticsIds);
        $this->content['AllPayLogisticsID'] = implode(',', $this->allPayLogisticsIDs);

        return $this;
    }

    /**
     * 設定 CVS 出貨單號（7-ELEVEN C2C 專用，單筆）。
     *
     * @param string $paymentNo 出貨單號
     * @return static
     */
    public function setCVSPaymentNo(string $paymentNo): static
    {
        $this->cvsPaymentNos = [$paymentNo];
        $this->content['CVSPaymentNo'] = $paymentNo;

        return $this;
    }

    /**
     * 設定 CVS 出貨單號（7-ELEVEN C2C 專用，批次）。
     *
     * @param array<int, string> $paymentNos 出貨單號陣列
     * @return static
     */
    public function setCVSPaymentNos(array $paymentNos): static
    {
        $this->cvsPaymentNos = array_values($paymentNos);
        $this->content['CVSPaymentNo'] = implode(',', $this->cvsPaymentNos);

        return $this;
    }

    /**
     * 設定 CVS 驗證碼（7-ELEVEN C2C 專用，單筆）。
     *
     * @param string $validationNo 驗證碼
     * @return static
     */
    public function setCVSValidationNo(string $validationNo): static
    {
        $this->cvsValidationNos = [$validationNo];
        $this->content['CVSValidationNo'] = $validationNo;

        return $this;
    }

    /**
     * 設定 CVS 驗證碼（7-ELEVEN C2C 專用，批次）。
     *
     * @param array<int, string> $validationNos 驗證碼陣列
     * @return static
     */
    public function setCVSValidationNos(array $validationNos): static
    {
        $this->cvsValidationNos = array_values($validationNos);
        $this->content['CVSValidationNo'] = implode(',', $this->cvsValidationNos);

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function validation(): void
    {
        $this->validateBaseParams();

        if (empty($this->content['LogisticsSubType'])) {
            throw LogisticsException::required('LogisticsSubType');
        }

        $subType = LogisticsSubType::tryFrom($this->content['LogisticsSubType']);

        // 7-ELEVEN C2C 需要 CVSPaymentNo 和 CVSValidationNo
        if ($subType === LogisticsSubType::UNIMART_C2C) {
            if (empty($this->cvsPaymentNos)) {
                throw LogisticsException::required('CVSPaymentNo');
            }
            if (empty($this->cvsValidationNos)) {
                throw LogisticsException::required('CVSValidationNo');
            }
        } else {
            // 其他超商需要 AllPayLogisticsID
            if (empty($this->allPayLogisticsIDs)) {
                throw LogisticsException::required('AllPayLogisticsID');
            }
        }
    }

    // ========== 便利方法 ==========

    /**
     * 列印 7-ELEVEN C2C 託運單。
     *
     * @return static
     */
    public function useUnimartC2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::UNIMART_C2C);
    }

    /**
     * 列印全家 C2C 託運單。
     *
     * @return static
     */
    public function useFamiC2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::FAMI_C2C);
    }

    /**
     * 列印萊爾富 C2C 託運單。
     *
     * @return static
     */
    public function useHilifeC2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::HILIFE_C2C);
    }

    /**
     * 列印 OK超商 C2C 託運單。
     *
     * @return static
     */
    public function useOkmartC2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::OKMART_C2C);
    }

    /**
     * 設定 7-ELEVEN C2C 列印資訊。
     *
     * @param string $paymentNo 出貨單號
     * @param string $validationNo 驗證碼
     * @return static
     */
    public function forUnimart(string $paymentNo, string $validationNo): static
    {
        return $this->useUnimartC2C()
            ->setCVSPaymentNo($paymentNo)
            ->setCVSValidationNo($validationNo);
    }
}
