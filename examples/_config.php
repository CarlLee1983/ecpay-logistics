<?php

/**
 * 範例設定檔。
 *
 * 請依據實際情況修改以下設定。
 */

return [
    // 測試環境網址
    'server' => 'https://logistics-stage.ecpay.com.tw',

    // C2C 測試商店資訊
    'c2c' => [
        'merchant_id' => '2000132',
        'hash_key' => '5294y06JbISpM5x9',
        'hash_iv' => 'v77hoKGq4kWxNNIS',
    ],

    // B2C 測試商店資訊
    'b2c' => [
        'merchant_id' => '2000933',
        'hash_key' => 'XBERn1YOvpM9nfZc',
        'hash_iv' => 'h1ONHk4P4yqbl5LK',
    ],

    // 回傳網址（請改成自己的網址）
    'server_reply_url' => 'https://your-domain.com/logistics/callback',
    'client_reply_url' => 'https://your-domain.com/logistics/return',
];
