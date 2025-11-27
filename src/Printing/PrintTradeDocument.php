<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Printing;

use CarlLee\EcPayLogistics\Content;
use CarlLee\EcPayLogistics\Exceptions\LogisticsException;
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;

/**
 * 列印託運單。
 *
 * 適用於 B2C 超商（含測標）及宅配。
 */
class PrintTradeDocument extends Content
{
    /**
     * API 請求路徑。
     */
    protected string $requestPath = '/helper/printTradeDocument';

    /**
     * 物流交易編號列表。
     *
     * @var array<int, string>
     */
    protected array $allPayLogisticsIDs = [];

    /**
     * @inheritDoc
     */
    protected function initContent(): void
    {
        parent::initContent();
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
     * 新增物流交易編號。
     *
     * @param string $logisticsId 物流交易編號
     * @return static
     */
    public function addLogisticsID(string $logisticsId): static
    {
        $this->allPayLogisticsIDs[] = $logisticsId;
        $this->content['AllPayLogisticsID'] = implode(',', $this->allPayLogisticsIDs);

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
        // 僅 B2C 和宅配可使用此列印功能
        if ($subType->isC2C()) {
            throw LogisticsException::invalid('LogisticsSubType', '請使用 PrintCvsDocument 列印 C2C 託運單');
        }

        $this->content['LogisticsSubType'] = $subType->value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function validation(): void
    {
        $this->validateBaseParams();

        if (empty($this->allPayLogisticsIDs)) {
            throw LogisticsException::required('AllPayLogisticsID');
        }
    }

    // ========== 便利方法 ==========

    /**
     * 列印 7-ELEVEN B2C 託運單。
     *
     * @return static
     */
    public function useUnimartB2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::UNIMART);
    }

    /**
     * 列印全家 B2C 託運單。
     *
     * @return static
     */
    public function useFamiB2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::FAMI);
    }

    /**
     * 列印萊爾富 B2C 託運單。
     *
     * @return static
     */
    public function useHilifeB2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::HILIFE);
    }

    /**
     * 列印黑貓宅配託運單。
     *
     * @return static
     */
    public function useTcat(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::TCAT);
    }

    /**
     * 列印中華郵政託運單。
     *
     * @return static
     */
    public function usePost(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::POST);
    }
}
