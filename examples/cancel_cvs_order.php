<?php

/**
 * 取消超商物流訂單範例。
 *
 * 此範例展示如何取消超商訂單。
 *
 * 注意：僅適用於 C2C 7-ELEVEN。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\EcPayLogistics\Factories\OperationFactory;

// 載入設定
$config = require __DIR__ . '/_config.php';

// 建立工廠（使用 C2C 測試帳號）
$factory = new OperationFactory([
    'merchant_id' => $config['c2c']['merchant_id'],
    'hash_key' => $config['c2c']['hash_key'],
    'hash_iv' => $config['c2c']['hash_iv'],
    'server_url' => $config['server'],
]);

// 取消 7-ELEVEN C2C 訂單
$cancel = $factory->make('cvs.cancel')
    ->setAllPayLogisticsID('1234567890')   // 替換成實際的物流交易編號
    ->setCVSPaymentNo('ABC123456')         // 替換成實際的出貨單號
    ->setCVSValidationNo('1234');          // 替換成實際的驗證碼

try {
    $response = $cancel->send();

    echo "=== 取消 C2C 7-ELEVEN 訂單 ===\n";
    echo '回傳代碼：' . $response->getRtnCode() . "\n";
    echo '回傳訊息：' . $response->getRtnMsg() . "\n";

    if ($response->isSuccess()) {
        echo "訂單已成功取消\n";
    }
} catch (Exception $e) {
    echo '錯誤：' . $e->getMessage() . "\n";
}

