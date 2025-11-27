<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Queries;

use CarlLee\EcPayLogistics\Content;
use CarlLee\EcPayLogistics\Exceptions\LogisticsException;
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;
use CarlLee\EcPayLogistics\Parameter\StoreType;

/**
 * 取得門市清單。
 *
 * 透過 API 取得超商門市資料（非電子地圖）。
 */
class GetStoreList extends Content
{
    /**
     * API 請求路徑。
     */
    protected string $requestPath = '/Express/GetStoreList';

    /**
     * @inheritDoc
     */
    protected function initContent(): void
    {
        parent::initContent();

        $this->content['StoreType'] = StoreType::PICKUP_ONLY->value;
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
     * 設定門市類型。
     *
     * @param StoreType $storeType 門市類型
     * @return static
     */
    public function setStoreType(StoreType $storeType): static
    {
        $this->content['StoreType'] = $storeType->value;

        return $this;
    }

    /**
     * 設定關鍵字搜尋（可用地址或店名）。
     *
     * @param string $keyword 關鍵字
     * @return static
     */
    public function setKeyword(string $keyword): static
    {
        $this->content['Keyword'] = $keyword;

        return $this;
    }

    /**
     * 設定郵遞區號搜尋。
     *
     * @param string $zipCode 郵遞區號
     * @return static
     */
    public function setZipCode(string $zipCode): static
    {
        $this->content['ZipCode'] = $zipCode;

        return $this;
    }

    /**
     * 設定縣市搜尋。
     *
     * @param string $city 縣市
     * @return static
     */
    public function setCity(string $city): static
    {
        $this->content['City'] = $city;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function validation(): void
    {
        $this->validateBaseParams();

        if (empty($this->content['LogisticsSubType'])) {
            throw LogisticsException::required('LogisticsSubType');
        }

        // 至少需要一種搜尋條件
        $hasSearchCriteria = !empty($this->content['Keyword'])
            || !empty($this->content['ZipCode'])
            || !empty($this->content['City']);

        if (!$hasSearchCriteria) {
            throw LogisticsException::required('Keyword、ZipCode 或 City 至少填一項');
        }
    }

    // ========== 便利方法 ==========

    /**
     * 搜尋 7-ELEVEN 門市。
     *
     * @param bool $isC2C 是否為 C2C
     * @return static
     */
    public function searchUnimart(bool $isC2C = true): static
    {
        return $this->setLogisticsSubType(
            $isC2C ? LogisticsSubType::UNIMART_C2C : LogisticsSubType::UNIMART
        );
    }

    /**
     * 搜尋全家門市。
     *
     * @param bool $isC2C 是否為 C2C
     * @return static
     */
    public function searchFami(bool $isC2C = true): static
    {
        return $this->setLogisticsSubType(
            $isC2C ? LogisticsSubType::FAMI_C2C : LogisticsSubType::FAMI
        );
    }

    /**
     * 搜尋萊爾富門市。
     *
     * @param bool $isC2C 是否為 C2C
     * @return static
     */
    public function searchHilife(bool $isC2C = true): static
    {
        return $this->setLogisticsSubType(
            $isC2C ? LogisticsSubType::HILIFE_C2C : LogisticsSubType::HILIFE
        );
    }

    /**
     * 搜尋 OK超商門市。
     *
     * @return static
     */
    public function searchOkmart(): static
    {
        return $this->setLogisticsSubType(LogisticsSubType::OKMART_C2C);
    }

    /**
     * 僅搜尋取出店。
     *
     * @return static
     */
    public function pickupOnly(): static
    {
        return $this->setStoreType(StoreType::PICKUP_ONLY);
    }

    /**
     * 搜尋取出店與退貨店。
     *
     * @return static
     */
    public function pickupAndReturn(): static
    {
        return $this->setStoreType(StoreType::PICKUP_AND_RETURN);
    }

    /**
     * 僅搜尋退貨店。
     *
     * @return static
     */
    public function returnOnly(): static
    {
        return $this->setStoreType(StoreType::RETURN_ONLY);
    }

    /**
     * 依關鍵字搜尋。
     *
     * @param string $keyword 關鍵字
     * @return static
     */
    public function byKeyword(string $keyword): static
    {
        return $this->setKeyword($keyword);
    }

    /**
     * 依郵遞區號搜尋。
     *
     * @param string $zipCode 郵遞區號
     * @return static
     */
    public function byZipCode(string $zipCode): static
    {
        return $this->setZipCode($zipCode);
    }

    /**
     * 依縣市搜尋。
     *
     * @param string $city 縣市
     * @return static
     */
    public function byCity(string $city): static
    {
        return $this->setCity($city);
    }
}
