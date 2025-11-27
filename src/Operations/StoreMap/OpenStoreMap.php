<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Operations\StoreMap;

use CarlLee\EcPayLogistics\Content;
use CarlLee\EcPayLogistics\Exceptions\LogisticsException;
use CarlLee\EcPayLogistics\Parameter\Device;
use CarlLee\EcPayLogistics\Parameter\IsCollection;
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;
use CarlLee\EcPayLogistics\Parameter\LogisticsType;

/**
 * 門市電子地圖。
 *
 * 開啟綠界的門市電子地圖，讓消費者選擇取貨門市。
 * 此功能需要透過瀏覽器導向，而非 Server-to-Server。
 */
class OpenStoreMap extends Content
{
    /**
     * API 請求路徑。
     */
    protected string $requestPath = '/Express/map';

    /**
     * @inheritDoc
     */
    protected function initContent(): void
    {
        parent::initContent();

        $this->content['LogisticsType'] = LogisticsType::CVS->value;
        $this->content['LogisticsSubType'] = LogisticsSubType::UNIMART_C2C->value;
        $this->content['IsCollection'] = IsCollection::NO->value;
        $this->content['Device'] = Device::PC->value;
    }

    /**
     * 設定物流類型。
     *
     * @param LogisticsType $type 物流類型
     * @return static
     */
    public function setLogisticsType(LogisticsType $type): static
    {
        $this->content['LogisticsType'] = $type->value;

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
        $this->content['LogisticsSubType'] = $subType->value;

        // 自動設定對應的物流類型
        $this->content['LogisticsType'] = $subType->getLogisticsType()->value;

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
     * 設定額外資訊（會原封不動回傳）。
     *
     * @param string $extraData 額外資訊
     * @return static
     */
    public function setExtraData(string $extraData): static
    {
        $this->content['ExtraData'] = $extraData;

        return $this;
    }

    /**
     * 設定裝置類型。
     *
     * @param Device|int $device 裝置類型
     * @return static
     */
    public function setDevice(Device|int $device): static
    {
        if ($device instanceof Device) {
            $this->content['Device'] = $device->value;
        } else {
            $this->content['Device'] = $device;
        }

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

        if (empty($this->content['LogisticsType'])) {
            throw LogisticsException::required('LogisticsType');
        }

        if (empty($this->content['LogisticsSubType'])) {
            throw LogisticsException::required('LogisticsSubType');
        }

        if (!isset($this->content['IsCollection'])) {
            throw LogisticsException::required('IsCollection');
        }

        if (empty($this->content['ServerReplyURL'])) {
            throw LogisticsException::required('ServerReplyURL');
        }
    }

    /**
     * 設定 7-ELEVEN C2C。
     *
     * @return static
     */
    public function useUnimartC2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::UNIMART_C2C);
    }

    /**
     * 設定全家 C2C。
     *
     * @return static
     */
    public function useFamiC2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::FAMI_C2C);
    }

    /**
     * 設定萊爾富 C2C。
     *
     * @return static
     */
    public function useHilifeC2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::HILIFE_C2C);
    }

    /**
     * 設定 OK超商 C2C。
     *
     * @return static
     */
    public function useOkmartC2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::OKMART_C2C);
    }

    /**
     * 設定 7-ELEVEN B2C。
     *
     * @return static
     */
    public function useUnimartB2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::UNIMART);
    }

    /**
     * 設定全家 B2C。
     *
     * @return static
     */
    public function useFamiB2C(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::FAMI);
    }

    /**
     * 設定萊爾富 B2C。
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
     * @return static
     */
    public function withCollection(): static
    {
        return $this->setIsCollection(IsCollection::YES);
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

    /**
     * 使用手機版介面。
     *
     * @return static
     */
    public function useMobileDevice(): static
    {
        return $this->setDevice(Device::MOBILE);
    }

    /**
     * 使用桌機版介面。
     *
     * @return static
     */
    public function usePCDevice(): static
    {
        return $this->setDevice(Device::PC);
    }
}
