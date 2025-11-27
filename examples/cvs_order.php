<?php

/**
 * 建立超商物流訂單範例。
 *
 * 此範例展示如何建立超商取貨訂單（C2C 及 B2C）。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\EcPayLogistics\Factories\OperationFactory;
use CarlLee\EcPayLogistics\Parameter\IsCollection;
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;

// 載入設定
$config = require __DIR__ . '/_config.php';

// ========== C2C 超商訂單 ==========

// 建立工廠（使用 C2C 測試帳號）
$factoryC2C = new OperationFactory([
    'merchant_id' => $config['c2c']['merchant_id'],
    'hash_key' => $config['c2c']['hash_key'],
    'hash_iv' => $config['c2c']['hash_iv'],
    'server_url' => $config['server'],
]);

// 建立 7-ELEVEN C2C 訂單
$orderC2C = $factoryC2C->make('cvs.create')
    ->setMerchantTradeNo('C2C_' . time())
    ->setLogisticsSubType(LogisticsSubType::UNIMART_C2C)
    ->setGoodsAmount(100)
    ->setGoodsName('測試商品')
    ->setIsCollection(IsCollection::NO)
    // 寄件人資訊
    ->setSenderName('測試寄件人')
    ->setSenderCellPhone('0912345678')
    // 收件人資訊
    ->setReceiverName('測試收件人')
    ->setReceiverCellPhone('0987654321')
    ->setReceiverStoreID('991182')  // 門市代號（從門市地圖取得）
    ->setServerReplyURL($config['server_reply_url']);

try {
    $responseC2C = $orderC2C->send();

    echo "=== C2C 超商訂單 ===\n";
    echo '回傳代碼：' . $responseC2C->getRtnCode() . "\n";
    echo '回傳訊息：' . $responseC2C->getRtnMsg() . "\n";

    if ($responseC2C->isSuccess()) {
        echo '物流交易編號：' . $responseC2C->getAllPayLogisticsID() . "\n";
        echo 'CVS 出貨單號：' . $responseC2C->getCVSPaymentNo() . "\n";
        echo 'CVS 驗證碼：' . $responseC2C->getCVSValidationNo() . "\n";
    }
} catch (Exception $e) {
    echo '錯誤：' . $e->getMessage() . "\n";
}

echo "\n";

// ========== B2C 超商訂單 ==========

// 建立工廠（使用 B2C 測試帳號）
$factoryB2C = new OperationFactory([
    'merchant_id' => $config['b2c']['merchant_id'],
    'hash_key' => $config['b2c']['hash_key'],
    'hash_iv' => $config['b2c']['hash_iv'],
    'server_url' => $config['server'],
]);

// 建立全家 B2C 訂單
$orderB2C = $factoryB2C->make('cvs.create')
    ->setMerchantTradeNo('B2C_' . time())
    ->setLogisticsSubType(LogisticsSubType::FAMI)
    ->setGoodsAmount(500)
    ->setGoodsName('測試商品')
    ->setIsCollection(IsCollection::NO)
    // 寄件人資訊
    ->setSenderName('測試公司')
    ->setSenderPhone('02-12345678')
    ->setSenderCellPhone('0912345678')
    // 收件人資訊
    ->setReceiverName('測試收件人')
    ->setReceiverCellPhone('0987654321')
    ->setReceiverEmail('test@example.com')
    ->setReceiverStoreID('001779')  // 門市代號
    ->setServerReplyURL($config['server_reply_url']);

try {
    $responseB2C = $orderB2C->send();

    echo "=== B2C 超商訂單 ===\n";
    echo '回傳代碼：' . $responseB2C->getRtnCode() . "\n";
    echo '回傳訊息：' . $responseB2C->getRtnMsg() . "\n";

    if ($responseB2C->isSuccess()) {
        echo '物流交易編號：' . $responseB2C->getAllPayLogisticsID() . "\n";
        echo '貨運單號：' . $responseB2C->getBookingNote() . "\n";
    }
} catch (Exception $e) {
    echo '錯誤：' . $e->getMessage() . "\n";
}
