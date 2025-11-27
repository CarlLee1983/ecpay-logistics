<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Queries;

use CarlLee\EcPayLogistics\Content;
use CarlLee\EcPayLogistics\Exceptions\LogisticsException;

/**
 * 查詢物流訂單。
 */
class QueryLogisticsOrder extends Content
{
    /**
     * API 請求路徑。
     */
    protected string $requestPath = '/Helper/QueryLogisticsTradeInfo/V4';

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function initContent(): void
    {
        parent::initContent();

        $this->content['TimeStamp'] = time();
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
     * 設定時間戳記。
     *
     * @param int $timestamp 時間戳記
     * @return static
     */
    public function setTimeStamp(int $timestamp): static
    {
        $this->content['TimeStamp'] = $timestamp;

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

        if (empty($this->content['TimeStamp'])) {
            throw LogisticsException::required('TimeStamp');
        }
    }
}
