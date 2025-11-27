<?php

/**
 * 查詢物流訂單範例。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\EcPayLogistics\Factories\OperationFactory;

// 載入設定
$config = require __DIR__ . '/_config.php';

// 建立工廠
$factory = new OperationFactory([
    'merchant_id' => $config['c2c']['merchant_id'],
    'hash_key' => $config['c2c']['hash_key'],
    'hash_iv' => $config['c2c']['hash_iv'],
    'server_url' => $config['server'],
]);

// 查詢物流訂單
$query = $factory->make('queries.order')
    ->setAllPayLogisticsID('1234567890');  // 請替換成實際的物流交易編號

try {
    $response = $query->send();

    echo "=== 查詢物流訂單 ===\n";
    echo '回傳代碼：' . $response->getRtnCode() . "\n";
    echo '回傳訊息：' . $response->getRtnMsg() . "\n";

    if ($response->isSuccess()) {
        $data = $response->getData();
        echo '物流交易編號：' . ($data['AllPayLogisticsID'] ?? '') . "\n";
        echo '物流類型：' . ($data['LogisticsType'] ?? '') . "\n";
        echo '物流子類型：' . ($data['LogisticsSubType'] ?? '') . "\n";
        echo '商品金額：' . ($data['GoodsAmount'] ?? '') . "\n";
        echo '收件人姓名：' . ($data['ReceiverName'] ?? '') . "\n";
        echo '收件人地址：' . ($data['ReceiverAddress'] ?? '') . "\n";
    }
} catch (Exception $e) {
    echo '錯誤：' . $e->getMessage() . "\n";
}
