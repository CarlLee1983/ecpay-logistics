<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Factories;

use CarlLee\EcPayLogistics\Content;
use CarlLee\EcPayLogistics\Infrastructure\CheckMacEncoder;
use InvalidArgumentException;

/**
 * 物流操作工廠。
 *
 * 負責建立各種物流操作類別的實例。
 */
class OperationFactory implements OperationFactoryInterface
{
    /**
     * 預設別名對應。
     */
    private const DEFAULT_ALIASES = [
        // 門市地圖
        'store_map' => 'Operations\\StoreMap\\OpenStoreMap',

        // 超商物流
        'cvs.create' => 'Operations\\Cvs\\CreateCvsOrder',
        'cvs.update' => 'Operations\\Cvs\\UpdateCvsOrder',
        'cvs.cancel' => 'Operations\\Cvs\\CancelCvsOrder',
        'cvs.return' => 'Operations\\Cvs\\ReturnCvsOrder',

        // 宅配物流
        'home.create' => 'Operations\\Home\\CreateHomeOrder',
        'home.return' => 'Operations\\Home\\ReturnHomeOrder',

        // 查詢
        'queries.order' => 'Queries\\QueryLogisticsOrder',
        'queries.store_list' => 'Queries\\GetStoreList',

        // 列印
        'printing.trade' => 'Printing\\PrintTradeDocument',
        'printing.cvs' => 'Printing\\PrintCvsDocument',
    ];

    /**
     * 群組別名對應。
     */
    private const GROUP_MAP = [
        'operations' => 'Operations',
        'operation' => 'Operations',
        'ops' => 'Operations',
        'queries' => 'Queries',
        'query' => 'Queries',
        'printing' => 'Printing',
        'print' => 'Printing',
        'cvs' => 'Operations\\Cvs',
        'home' => 'Operations\\Home',
        'store_map' => 'Operations\\StoreMap',
    ];

    /**
     * 商店憑證設定。
     *
     * @var array{merchant_id: string, hash_key: string, hash_iv: string}
     */
    protected array $credentials = [
        'merchant_id' => '',
        'hash_key' => '',
        'hash_iv' => '',
    ];

    /**
     * 伺服器網址。
     */
    protected string $serverUrl = 'https://logistics-stage.ecpay.com.tw';

    /**
     * 自訂別名對應的實際類別。
     *
     * @var array<string, string>
     */
    protected array $aliases = [];

    /**
     * 自訂生成器。
     *
     * @var array<string, callable>
     */
    protected array $resolvers = [];

    /**
     * 共用初始化程式。
     *
     * @var callable[]
     */
    protected array $initializers = [];

    /**
     * 建立工廠實例。
     *
     * @param array{
     *     merchant_id?: string,
     *     hash_key?: string,
     *     hash_iv?: string,
     *     server_url?: string,
     *     aliases?: array<string, string>,
     *     resolvers?: array<string, callable>,
     *     initializers?: callable[]
     * } $config 設定
     */
    public function __construct(array $config = [])
    {
        $this->setCredentials(
            (string) ($config['merchant_id'] ?? ''),
            (string) ($config['hash_key'] ?? ''),
            (string) ($config['hash_iv'] ?? '')
        );

        if (isset($config['server_url'])) {
            $this->serverUrl = rtrim($config['server_url'], '/');
        }

        foreach ($config['aliases'] ?? [] as $alias => $class) {
            $this->alias($alias, $class);
        }

        foreach ($config['resolvers'] ?? [] as $alias => $resolver) {
            $this->extend($alias, $resolver);
        }

        foreach ($config['initializers'] ?? [] as $initializer) {
            $this->addInitializer($initializer);
        }
    }

    /**
     * @inheritDoc
     */
    public function make(string $target, array $parameters = []): Content
    {
        $key = $this->normalizeKey($target);

        if (isset($this->resolvers[$key])) {
            $content = $this->resolvers[$key]($parameters, $this);

            if (!$content instanceof Content) {
                throw new InvalidArgumentException("自訂解析 {$target} 必須回傳 Content。");
            }

            return $this->initialize($content);
        }

        $class = $this->resolveClassName($target, $key);

        $instance = $this->buildInstance($class, $parameters);

        return $this->initialize($instance);
    }

    /**
     * @inheritDoc
     */
    public function extend(string $alias, callable $resolver): void
    {
        $this->resolvers[$this->normalizeKey($alias)] = $resolver;
    }

    /**
     * @inheritDoc
     */
    public function alias(string $alias, string $class): void
    {
        $key = $this->normalizeKey($alias);
        $this->aliases[$key] = $class;
    }

    /**
     * @inheritDoc
     */
    public function addInitializer(callable $initializer): void
    {
        $this->initializers[] = $initializer;
    }

    /**
     * @inheritDoc
     */
    public function setCredentials(string $merchantId, string $hashKey, string $hashIV): void
    {
        $this->credentials = [
            'merchant_id' => $merchantId,
            'hash_key' => $hashKey,
            'hash_iv' => $hashIV,
        ];
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
     * 取得憑證資訊。
     *
     * @return array{merchant_id: string, hash_key: string, hash_iv: string}
     */
    public function getCredentials(): array
    {
        return $this->credentials;
    }

    /**
     * 取得 CheckMac 編碼器。
     *
     * @return CheckMacEncoder
     */
    public function getEncoder(): CheckMacEncoder
    {
        return new CheckMacEncoder(
            $this->credentials['hash_key'],
            $this->credentials['hash_iv']
        );
    }

    /**
     * 取得伺服器網址。
     *
     * @return string
     */
    public function getServerUrl(): string
    {
        return $this->serverUrl;
    }

    /**
     * 建立實際物件。
     *
     * @param class-string $class 類別名稱
     * @param array<int, mixed> $parameters 建構參數
     * @return Content
     */
    protected function buildInstance(string $class, array $parameters): Content
    {
        if (!is_subclass_of($class, Content::class)) {
            throw new InvalidArgumentException("{$class} 必須繼承 Content");
        }

        if (empty($parameters)) {
            $parameters = [
                $this->credentials['merchant_id'],
                $this->credentials['hash_key'],
                $this->credentials['hash_iv'],
            ];
        }

        /** @var Content $instance */
        $instance = new $class(...array_values($parameters));

        // 設定伺服器網址
        $instance->setServerUrl($this->serverUrl);

        return $instance;
    }

    /**
     * 執行所有初始化程式。
     *
     * @param Content $content
     * @return Content
     */
    protected function initialize(Content $content): Content
    {
        foreach ($this->initializers as $initializer) {
            $initializer($content);
        }

        return $content;
    }

    /**
     * 解析別名並取得實際類別名稱。
     *
     * @param string $target 目標
     * @param string|null $normalized 正規化後的 key
     * @return class-string
     */
    protected function resolveClassName(string $target, ?string $normalized = null): string
    {
        $key = $normalized ?? $this->normalizeKey($target);

        // 檢查自訂別名
        if (isset($this->aliases[$key])) {
            $class = $this->aliases[$key];
            if (!class_exists($class)) {
                throw new InvalidArgumentException("別名 {$key} 指向的類別 {$class} 不存在。");
            }

            return $class;
        }

        // 檢查預設別名
        if (isset(self::DEFAULT_ALIASES[$key])) {
            $class = $this->getBaseNamespace() . '\\' . self::DEFAULT_ALIASES[$key];
            if (class_exists($class)) {
                return $class;
            }
        }

        // 嘗試完整類別名稱
        $trimmed = trim($target);
        $classCandidate = ltrim($trimmed, '\\');

        if ($classCandidate !== '' && class_exists($classCandidate)) {
            return $classCandidate;
        }

        // 從別名解析群組和名稱
        [$group, $name] = $this->parseAlias($key);
        $class = sprintf('%s\\%s\\%s', $this->getBaseNamespace(), $group, $this->studly($name));

        if (!class_exists($class)) {
            throw new InvalidArgumentException("找不到 {$target} 對應的類別 {$class}。");
        }

        return $class;
    }

    /**
     * 取得基底命名空間。
     *
     * @return string
     */
    protected function getBaseNamespace(): string
    {
        return 'CarlLee\\EcPayLogistics';
    }

    /**
     * 分析別名，回傳群組與名稱。
     *
     * @param string $alias 別名
     * @return array{string, string} [群組, 名稱]
     */
    protected function parseAlias(string $alias): array
    {
        if ($alias === '') {
            throw new InvalidArgumentException('別名不得為空字串。');
        }

        if (strpos($alias, '.') === false) {
            return ['Operations', $alias];
        }

        [$groupKey, $name] = explode('.', $alias, 2);
        $groupKey = strtolower($groupKey);
        $group = self::GROUP_MAP[$groupKey] ?? 'Operations';

        if ($name === '') {
            throw new InvalidArgumentException('別名需包含實際類別名稱。');
        }

        return [$group, $name];
    }

    /**
     * 將字串轉為 StudlyCase。
     *
     * @param string $value 字串
     * @return string
     */
    protected function studly(string $value): string
    {
        $value = str_replace(['.', '-'], '_', $value);
        $segments = explode('_', $value);
        $studly = '';

        foreach ($segments as $segment) {
            if ($segment === '') {
                continue;
            }

            $studly .= ucfirst(strtolower($segment));
        }

        return $studly;
    }

    /**
     * 正規化 key。
     *
     * @param string $value 字串
     * @return string
     */
    protected function normalizeKey(string $value): string
    {
        return strtolower(trim($value));
    }
}
