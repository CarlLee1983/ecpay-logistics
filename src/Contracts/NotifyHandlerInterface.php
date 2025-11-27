<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Contracts;

/**
 * 通知處理介面。
 */
interface NotifyHandlerInterface
{
    /**
     * 驗證通知資料。
     *
     * @param array<string, mixed> $data 通知資料
     * @return bool
     */
    public function verify(array $data): bool;

    /**
     * 取得通知資料。
     *
     * @return array<string, mixed>
     */
    public function getData(): array;

    /**
     * 是否成功。
     *
     * @return bool
     */
    public function isSuccess(): bool;

    /**
     * 取得回傳代碼。
     *
     * @return string
     */
    public function getRtnCode(): string;

    /**
     * 取得回傳訊息。
     *
     * @return string
     */
    public function getRtnMsg(): string;

    /**
     * 產生成功回應。
     *
     * @return string
     */
    public function getSuccessResponse(): string;
}
