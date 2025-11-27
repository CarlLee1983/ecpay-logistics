<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics;

use CarlLee\EcPayLogistics\Contracts\LogisticsInterface;
use CarlLee\EcPayLogistics\Exceptions\LogisticsException;
use CarlLee\EcPayLogistics\Infrastructure\CheckMacEncoder;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

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
     * PSR-3 日誌記錄器。
     */
    protected LoggerInterface $logger;

    /**
     * HTTP 請求重試次數。
     */
    protected int $retryAttempts = 3;

    /**
     * HTTP 請求重試延遲（毫秒）。
     */
    protected int $retryDelay = 1000;

    /**
     * 建立 Content 實例。
     *
     * @param string $merchantId 特店編號
     * @param string $hashKey HashKey
     * @param string $hashIV HashIV
     */
    public function __construct(string $merchantId = '', string $hashKey = '', string $hashIV = '')
    {
        $this->logger = new NullLogger();

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
     * 設定日誌記錄器。
     *
     * @param LoggerInterface $logger 日誌記錄器
     * @return static
     */
    public function setLogger(LoggerInterface $logger): static
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * 設定 HTTP 請求重試次數。
     *
     * @param int $attempts 重試次數
     * @return static
     */
    public function setRetryAttempts(int $attempts): static
    {
        $this->retryAttempts = max(0, $attempts);

        return $this;
    }

    /**
     * 設定 HTTP 請求重試延遲。
     *
     * @param int $milliseconds 延遲毫秒數
     * @return static
     */
    public function setRetryDelay(int $milliseconds): static
    {
        $this->retryDelay = max(0, $milliseconds);

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
            $stack = HandlerStack::create();

            // 加入重試中間件
            if ($this->retryAttempts > 0) {
                $stack->push(Middleware::retry(
                    $this->createRetryDecider(),
                    $this->createRetryDelay()
                ));
            }

            $this->httpClient = new Client([
                'handler' => $stack,
                'base_uri' => $this->serverUrl,
                'timeout' => 30,
                'connect_timeout' => 10,
            ]);
        }

        return $this->httpClient;
    }

    /**
     * 建立重試判斷函式。
     *
     * @return callable
     */
    protected function createRetryDecider(): callable
    {
        $maxRetries = $this->retryAttempts;
        $logger = $this->logger;

        return function (
            int $retries,
            RequestInterface $request,
            ?ResponseInterface $response = null,
            ?\Throwable $exception = null
        ) use ($maxRetries, $logger): bool {
            // 達到最大重試次數
            if ($retries >= $maxRetries) {
                return false;
            }

            // 連線錯誤時重試
            if ($exception instanceof ConnectException) {
                $logger->warning('ECPay API 連線失敗，準備重試', [
                    'retry' => $retries + 1,
                    'max_retries' => $maxRetries,
                    'error' => $exception->getMessage(),
                ]);
                return true;
            }

            // 伺服器錯誤（5xx）時重試
            if ($response !== null && $response->getStatusCode() >= 500) {
                $logger->warning('ECPay API 伺服器錯誤，準備重試', [
                    'retry' => $retries + 1,
                    'max_retries' => $maxRetries,
                    'status_code' => $response->getStatusCode(),
                ]);
                return true;
            }

            return false;
        };
    }

    /**
     * 建立重試延遲函式（指數退避）。
     *
     * @return callable
     */
    protected function createRetryDelay(): callable
    {
        $baseDelay = $this->retryDelay;

        return function (int $retries) use ($baseDelay): int {
            // 指數退避：1000ms, 2000ms, 4000ms...
            return $baseDelay * (int) pow(2, $retries);
        };
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

        // 記錄請求
        $this->logger->debug('ECPay API 請求', [
            'url' => $url,
            'path' => $this->requestPath,
            'payload' => $this->maskSensitiveData($content),
        ]);

        try {
            $response = $this->getHttpClient()->post($this->requestPath, [
                'form_params' => $content,
            ]);

            $body = (string) $response->getBody();

            // 記錄回應
            $this->logger->debug('ECPay API 回應', [
                'url' => $url,
                'status_code' => $response->getStatusCode(),
                'body' => $body,
            ]);

            return new Response($body, $this->getEncoder());
        } catch (GuzzleException $e) {
            // 記錄錯誤
            $this->logger->error('ECPay API 請求失敗', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            throw LogisticsException::httpError($e->getMessage());
        }
    }

    /**
     * 遮蔽敏感資料（用於日誌記錄）。
     *
     * @param array<string, mixed> $data 原始資料
     * @return array<string, mixed> 遮蔽後的資料
     */
    protected function maskSensitiveData(array $data): array
    {
        $sensitiveKeys = ['CheckMacValue', 'HashKey', 'HashIV'];

        foreach ($sensitiveKeys as $key) {
            if (isset($data[$key]) && is_string($data[$key])) {
                $value = $data[$key];
                $length = strlen($value);
                if ($length > 8) {
                    $data[$key] = substr($value, 0, 4) . str_repeat('*', $length - 8) . substr($value, -4);
                } else {
                    $data[$key] = str_repeat('*', $length);
                }
            }
        }

        return $data;
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
