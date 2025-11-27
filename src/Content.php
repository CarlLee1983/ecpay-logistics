<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics;

use CarlLee\EcPayLogistics\Contracts\LogisticsInterface;
use CarlLee\EcPayLogistics\Exceptions\LogisticsException;
use CarlLee\EcPayLogistics\Infrastructure\CheckMacEncoder;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * 物流 Content 基礎類別。
 *
 * 所有物流操作類別的基類。
 */
abstract class Content implements LogisticsInterface
{
    /**
     * 特店交易編號最大長度。
     */
    public const int MERCHANT_TRADE_NO_MAX_LENGTH = 20;

    /**
     * API 請求路徑。
     */
    protected string $requestPath = '';

    /**
     * 特店編號。
     */
    protected string $merchantID = '';

    /**
     * HashKey。
     */
    protected string $hashKey = '';

    /**
     * HashIV。
     */
    protected string $hashIV = '';

    /**
     * 平台商編號。
     */
    protected string $platformID = '';

    /**
     * 內容資料。
     *
     * @var array<string, mixed>
     */
    protected array $content = [];

    /**
     * CheckMac 編碼器。
     */
    protected ?CheckMacEncoder $encoder = null;

    /**
     * HTTP 用戶端。
     */
    protected ?Client $httpClient = null;

    /**
     * 伺服器網址。
     */
    protected string $serverUrl = 'https://logistics-stage.ecpay.com.tw';

    /**
     * 建立 Content 實例。
     *
     * @param string $merchantId 特店編號
     * @param string $hashKey HashKey
     * @param string $hashIV HashIV
     */
    public function __construct(string $merchantId = '', string $hashKey = '', string $hashIV = '')
    {
        $this->setMerchantID($merchantId);
        $this->setHashKey($hashKey);
        $this->setHashIV($hashIV);

        $this->initContent();
    }

    /**
     * 初始化內容。
     */
    protected function initContent(): void
    {
        $this->content = [
            'MerchantID' => $this->merchantID,
        ];
    }

    /**
     * 設定特店編號。
     *
     * @param string $id 特店編號
     * @return static
     */
    public function setMerchantID(string $id): static
    {
        $this->merchantID = $id;
        $this->content['MerchantID'] = $id;

        return $this;
    }

    /**
     * 取得特店編號。
     *
     * @return string
     */
    public function getMerchantID(): string
    {
        return $this->merchantID;
    }

    /**
     * 設定 HashKey。
     *
     * @param string $key HashKey
     * @return static
     */
    public function setHashKey(string $key): static
    {
        $this->hashKey = $key;

        return $this;
    }

    /**
     * 設定 HashIV。
     *
     * @param string $iv HashIV
     * @return static
     */
    public function setHashIV(string $iv): static
    {
        $this->hashIV = $iv;

        return $this;
    }

    /**
     * 設定平台商編號。
     *
     * @param string $id 平台商編號
     * @return static
     */
    public function setPlatformID(string $id): static
    {
        $this->platformID = $id;
        if (!empty($id)) {
            $this->content['PlatformID'] = $id;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMerchantTradeNo(string $tradeNo): static
    {
        if (strlen($tradeNo) > self::MERCHANT_TRADE_NO_MAX_LENGTH) {
            throw LogisticsException::tooLong('MerchantTradeNo', self::MERCHANT_TRADE_NO_MAX_LENGTH);
        }

        $this->content['MerchantTradeNo'] = $tradeNo;

        return $this;
    }

    /**
     * 設定交易日期時間。
     *
     * @param \DateTimeInterface|string $date 日期
     * @return static
     */
    public function setMerchantTradeDate(\DateTimeInterface|string $date): static
    {
        if ($date instanceof \DateTimeInterface) {
            $date = $date->format('Y/m/d H:i:s');
        }

        $this->content['MerchantTradeDate'] = $date;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setServerReplyURL(string $url): static
    {
        $this->content['ServerReplyURL'] = $url;

        return $this;
    }

    /**
     * 設定 Client 端回覆網址。
     *
     * @param string $url 網址
     * @return static
     */
    public function setClientReplyURL(string $url): static
    {
        $this->content['ClientReplyURL'] = $url;

        return $this;
    }

    /**
     * 設定備註。
     *
     * @param string $remark 備註
     * @return static
     */
    public function setRemark(string $remark): static
    {
        $this->content['Remark'] = $remark;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRequestPath(): string
    {
        return $this->requestPath;
    }

    /**
     * 取得 CheckMac 編碼器。
     *
     * @return CheckMacEncoder
     */
    public function getEncoder(): CheckMacEncoder
    {
        if ($this->encoder === null) {
            $this->encoder = new CheckMacEncoder($this->hashKey, $this->hashIV);
        }

        return $this->encoder;
    }

    /**
     * 設定自訂編碼器。
     *
     * @param CheckMacEncoder $encoder 編碼器
     * @return static
     */
    public function setEncoder(CheckMacEncoder $encoder): static
    {
        $this->encoder = $encoder;

        return $this;
    }

    /**
     * 設定伺服器網址。
     *
     * @param string $url 伺服器網址
     * @return static
     */
    public function setServerUrl(string $url): static
    {
        $this->serverUrl = rtrim($url, '/');

        return $this;
    }

    /**
     * 設定 HTTP 用戶端。
     *
     * @param Client $client HTTP 用戶端
     * @return static
     */
    public function setHttpClient(Client $client): static
    {
        $this->httpClient = $client;

        return $this;
    }

    /**
     * 取得 HTTP 用戶端。
     *
     * @return Client
     */
    protected function getHttpClient(): Client
    {
        if ($this->httpClient === null) {
            $this->httpClient = new Client([
                'base_uri' => $this->serverUrl,
                'timeout' => 30,
                'connect_timeout' => 10,
            ]);
        }

        return $this->httpClient;
    }

    /**
     * 驗證內容資料。
     *
     * @throws LogisticsException 當驗證失敗時
     */
    abstract protected function validation(): void;

    /**
     * @inheritDoc
     */
    public function getPayload(): array
    {
        $this->validation();

        // 同步 MerchantID
        $this->content['MerchantID'] = $this->merchantID;

        return $this->content;
    }

    /**
     * @inheritDoc
     */
    public function getContent(): array
    {
        $payload = $this->getPayload();
        $encoder = $this->getEncoder();

        return $encoder->encodePayload($payload);
    }

    /**
     * 發送 API 請求。
     *
     * @return Response
     * @throws LogisticsException 當請求失敗時
     */
    public function send(): Response
    {
        $content = $this->getContent();
        $url = $this->serverUrl . $this->requestPath;

        try {
            $response = $this->getHttpClient()->post($this->requestPath, [
                'form_params' => $content,
            ]);

            $body = (string) $response->getBody();

            return new Response($body, $this->getEncoder());
        } catch (GuzzleException $e) {
            throw LogisticsException::httpError($e->getMessage());
        }
    }

    /**
     * 驗證基礎參數。
     *
     * @throws LogisticsException 當驗證失敗時
     */
    protected function validateBaseParams(): void
    {
        if (empty($this->merchantID)) {
            throw LogisticsException::required('MerchantID');
        }

        if (empty($this->hashKey)) {
            throw LogisticsException::required('HashKey');
        }

        if (empty($this->hashIV)) {
            throw LogisticsException::required('HashIV');
        }
    }
}
