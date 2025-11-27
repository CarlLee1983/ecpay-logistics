<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | ECPay 物流介接環境
    |--------------------------------------------------------------------------
    |
    | 預設為綠界提供的測試環境。正式環境請改用
    | https://logistics.ecpay.com.tw
    |
    */
    'server' => env('ECPAY_LOGISTICS_SERVER', 'https://logistics-stage.ecpay.com.tw'),

    /*
    |--------------------------------------------------------------------------
    | 商店憑證設定
    |--------------------------------------------------------------------------
    |
    | MerchantID/HashKey/HashIV 為綠界提供的專屬金鑰。
    |
    | ⚠️  安全性警告：
    | - 請勿將金鑰資訊（HashKey、HashIV）存放或顯示於前端網頁，
    |   如 JavaScript、HTML、CSS 等，避免金鑰被盜取造成損失及資料外洩。
    | - 務必透過 .env 檔案配置金鑰，並確保 .env 不納入版本控制。
    | - 切勿將金鑰硬編碼於程式碼或設定檔中。
    |
    | 參考：https://developers.ecpay.com.tw/?p=7380
    |
    */
    'merchant_id' => env('ECPAY_LOGISTICS_MERCHANT_ID', ''),
    'hash_key' => env('ECPAY_LOGISTICS_HASH_KEY', ''),
    'hash_iv' => env('ECPAY_LOGISTICS_HASH_IV', ''),

    /*
    |--------------------------------------------------------------------------
    | 平台商編號
    |--------------------------------------------------------------------------
    |
    | 若為平台商模式，請設定此參數。
    |
    */
    'platform_id' => env('ECPAY_LOGISTICS_PLATFORM_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | 回傳網址設定
    |--------------------------------------------------------------------------
    |
    | server_reply_url: Server 端接收物流狀態通知網址
    | client_reply_url: Client 端導回網址
    |
    | ⚠️  注意事項：
    | - ServerReplyURL 必須是 Server 端 URL，用於接收綠界後端回傳的物流狀態
    | - 請確認 ServerReplyURL 已開放對外連線
    | - 僅支援 HTTP/HTTPS（80/443 port），指定其他 port 無法接收通知
    | - 不支援中文網址，請使用 punycode 編碼
    | - 收到通知後必須驗證 CheckMacValue 並回傳 1|OK
    |
    */
    'server_reply_url' => env('ECPAY_LOGISTICS_SERVER_REPLY_URL'),
    'client_reply_url' => env('ECPAY_LOGISTICS_CLIENT_REPLY_URL'),

    /*
    |--------------------------------------------------------------------------
    | HTTP 請求設定
    |--------------------------------------------------------------------------
    */
    'http' => [
        'timeout' => env('ECPAY_LOGISTICS_HTTP_TIMEOUT', 30),
        'connect_timeout' => env('ECPAY_LOGISTICS_HTTP_CONNECT_TIMEOUT', 10),
        'verify_ssl' => env('ECPAY_LOGISTICS_VERIFY_SSL', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | 工廠額外設定
    |--------------------------------------------------------------------------
    |
    | aliases: 自訂別名 => 類別對應
    | initializers: 統一設定初始化邏輯
    |
    */
    'factory' => [
        'aliases' => [
            // 'custom.order' => \App\Logistics\CustomOrder::class,
        ],
        'initializers' => [
            // \App\Logistics\Initializers\DefaultServerReplyUrl::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 便利綁定
    |--------------------------------------------------------------------------
    |
    | 可經由 app('ecpay.logistics.xxx') 解析對應操作物件。
    |
    */
    'bindings' => [
        'store_map' => 'store_map',
        'cvs_create' => 'cvs.create',
        'cvs_update' => 'cvs.update',
        'cvs_cancel' => 'cvs.cancel',
        'cvs_return' => 'cvs.return',
        'home_create' => 'home.create',
        'home_return' => 'home.return',
        'query_order' => 'queries.order',
        'store_list' => 'queries.store_list',
        'print_trade' => 'printing.trade',
        'print_cvs' => 'printing.cvs',
    ],
];

