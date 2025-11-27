<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Contracts;

/**
 * 物流操作介面。
 */
interface LogisticsInterface
{
    /**
     * 設定特店交易編號。
     *
     * @param string $tradeNo 交易編號
     * @return static
     */
    public function setMerchantTradeNo(string $tradeNo): static;

    /**
     * 設定 Server 端回覆網址。
     *
     * @param string $url 網址
     * @return static
     */
    public function setServerReplyURL(string $url): static;

    /**
     * 取得 API 請求路徑。
     *
     * @return string
     */
    public function getRequestPath(): string;

    /**
     * 取得 Payload（不含 CheckMacValue）。
     *
     * @return array<string, mixed>
     */
    public function getPayload(): array;

    /**
     * 取得完整內容（含 CheckMacValue）。
     *
     * @return array<string, mixed>
     */
    public function getContent(): array;
}
