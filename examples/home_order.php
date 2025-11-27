<?php

/**
 * 建立宅配物流訂單範例。
 *
 * 此範例展示如何建立宅配訂單（黑貓、中華郵政）。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\EcPayLogistics\Factories\OperationFactory;
use CarlLee\EcPayLogistics\Parameter\Distance;
use CarlLee\EcPayLogistics\Parameter\IsCollection;
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;
use CarlLee\EcPayLogistics\Parameter\ScheduledDeliveryTime;
use CarlLee\EcPayLogistics\Parameter\ScheduledPickupTime;
use CarlLee\EcPayLogistics\Parameter\Specification;
use CarlLee\EcPayLogistics\Parameter\Temperature;

// 載入設定
$config = require __DIR__ . '/_config.php';

// 建立工廠（使用 B2C 測試帳號）
$factory = new OperationFactory([
    'merchant_id' => $config['b2c']['merchant_id'],
    'hash_key' => $config['b2c']['hash_key'],
    'hash_iv' => $config['b2c']['hash_iv'],
    'server_url' => $config['server'],
]);

// ========== 黑貓宅配訂單 ==========

$orderTcat = $factory->make('home.create')
    ->setMerchantTradeNo('HOME_' . time())
    ->setLogisticsSubType(LogisticsSubType::TCAT)
    ->setGoodsAmount(1000)
    ->setGoodsName('測試商品')
    ->setIsCollection(IsCollection::NO)
    // 溫層與規格
    ->setTemperature(Temperature::ROOM)           // 常溫
    ->setDistance(Distance::SAME)                  // 同縣市
    ->setSpecification(Specification::SIZE_60)     // 60cm
    ->setScheduledPickupTime(ScheduledPickupTime::UNLIMITED)     // 取件時段不限
    ->setScheduledDeliveryTime(ScheduledDeliveryTime::UNLIMITED) // 配送時段不限
    // 寄件人資訊
    ->setSenderName('測試公司')
    ->setSenderPhone('02-12345678')
    ->setSenderCellPhone('0912345678')
    ->setSenderZipCode('106')
    ->setSenderAddress('台北市大安區忠孝東路100號')
    // 收件人資訊
    ->setReceiverName('測試收件人')
    ->setReceiverPhone('03-12345678')
    ->setReceiverCellPhone('0987654321')
    ->setReceiverZipCode('320')
    ->setReceiverAddress('桃園市中壢區中正路200號')
    ->setReceiverEmail('test@example.com')
    ->setServerReplyURL($config['server_reply_url']);

try {
    $responseTcat = $orderTcat->send();

    echo "=== 黑貓宅配訂單 ===\n";
    echo "回傳代碼：" . $responseTcat->getRtnCode() . "\n";
    echo "回傳訊息：" . $responseTcat->getRtnMsg() . "\n";

    if ($responseTcat->isSuccess()) {
        echo "物流交易編號：" . $responseTcat->getAllPayLogisticsID() . "\n";
        echo "貨運單號：" . $responseTcat->getBookingNote() . "\n";
    }
} catch (Exception $e) {
    echo "錯誤：" . $e->getMessage() . "\n";
}

echo "\n";

// ========== 中華郵政訂單 ==========

$orderPost = $factory->make('home.create')
    ->setMerchantTradeNo('POST_' . time())
    ->setLogisticsSubType(LogisticsSubType::POST)
    ->setGoodsAmount(500)
    ->setGoodsName('測試商品')
    // 寄件人資訊
    ->setSenderName('測試公司')
    ->setSenderPhone('02-12345678')
    ->setSenderZipCode('106')
    ->setSenderAddress('台北市大安區忠孝東路100號')
    // 收件人資訊
    ->setReceiverName('測試收件人')
    ->setReceiverPhone('03-12345678')
    ->setReceiverZipCode('320')
    ->setReceiverAddress('桃園市中壢區中正路200號')
    ->setServerReplyURL($config['server_reply_url']);

try {
    $responsePost = $orderPost->send();

    echo "=== 中華郵政訂單 ===\n";
    echo "回傳代碼：" . $responsePost->getRtnCode() . "\n";
    echo "回傳訊息：" . $responsePost->getRtnMsg() . "\n";

    if ($responsePost->isSuccess()) {
        echo "物流交易編號：" . $responsePost->getAllPayLogisticsID() . "\n";
        echo "貨運單號：" . $responsePost->getBookingNote() . "\n";
    }
} catch (Exception $e) {
    echo "錯誤：" . $e->getMessage() . "\n";
}

