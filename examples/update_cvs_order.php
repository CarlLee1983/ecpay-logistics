<?php

/**
 * 異動超商物流訂單範例。
 *
 * 此範例展示如何異動超商訂單的收件資訊。
 *
 * 適用於：
 * - B2C: 7-ELEVEN、萊爾富
 * - C2C: 7-ELEVEN、全家、OK、萊爾富
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\EcPayLogistics\Factories\OperationFactory;
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;

// 載入設定
$config = require __DIR__ . '/_config.php';

// ========== C2C 7-ELEVEN 異動訂單 ==========

$factoryC2C = new OperationFactory([
    'merchant_id' => $config['c2c']['merchant_id'],
    'hash_key' => $config['c2c']['hash_key'],
    'hash_iv' => $config['c2c']['hash_iv'],
    'server_url' => $config['server'],
]);

// 異動 7-ELEVEN C2C 訂單（需要 CVSPaymentNo 和 CVSValidationNo）
$updateC2C = $factoryC2C->make('cvs.update')
    ->setAllPayLogisticsID('1234567890')       // 替換成實際的物流交易編號
    ->setLogisticsSubType(LogisticsSubType::UNIMART_C2C)
    ->setCVSPaymentNo('ABC123456')             // 替換成實際的出貨單號
    ->setCVSValidationNo('1234')               // 替換成實際的驗證碼
    ->setStoreID('991182')                     // 新的門市代號
    ->setReceiverName('新收件人')
    ->setReceiverCellPhone('0911111111');

try {
    $responseC2C = $updateC2C->send();

    echo "=== C2C 7-ELEVEN 異動訂單 ===\n";
    echo '回傳代碼：' . $responseC2C->getRtnCode() . "\n";
    echo '回傳訊息：' . $responseC2C->getRtnMsg() . "\n";
} catch (Exception $e) {
    echo '錯誤：' . $e->getMessage() . "\n";
}

echo "\n";

// ========== B2C 全家異動訂單 ==========

$factoryB2C = new OperationFactory([
    'merchant_id' => $config['b2c']['merchant_id'],
    'hash_key' => $config['b2c']['hash_key'],
    'hash_iv' => $config['b2c']['hash_iv'],
    'server_url' => $config['server'],
]);

// 異動全家 B2C 訂單
$updateB2C = $factoryB2C->make('cvs.update')
    ->setAllPayLogisticsID('1234567890')       // 替換成實際的物流交易編號
    ->setLogisticsSubType(LogisticsSubType::FAMI)
    ->setShipmentDate(date('Y/m/d'))           // 出貨日期
    ->setReceiverStoreID('001779')             // 新的門市代號
    ->setReceiverName('新收件人')
    ->setReceiverCellPhone('0922222222');

try {
    $responseB2C = $updateB2C->send();

    echo "=== B2C 全家異動訂單 ===\n";
    echo '回傳代碼：' . $responseB2C->getRtnCode() . "\n";
    echo '回傳訊息：' . $responseB2C->getRtnMsg() . "\n";
} catch (Exception $e) {
    echo '錯誤：' . $e->getMessage() . "\n";
}

