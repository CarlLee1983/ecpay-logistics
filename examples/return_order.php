<?php

/**
 * 逆物流訂單範例。
 *
 * 此範例展示如何建立逆物流（退貨）訂單。
 *
 * 支援：
 * - B2C 超商逆物流：7-ELEVEN、全家、萊爾富
 * - 宅配逆物流：黑貓宅急便
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\EcPayLogistics\Factories\OperationFactory;
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

// ========== B2C 7-ELEVEN 逆物流 ==========

$returnUnimart = $factory->make('cvs.return')
    ->setAllPayLogisticsID('1234567890')   // 原訂單的物流交易編號
    ->useUnimartB2C()                       // 7-ELEVEN B2C
    ->setGoodsAmount(0)                     // 退貨不代收款項
    ->setGoodsName('退貨商品')
    ->setSenderName('買家姓名')
    ->setSenderPhone('0912345678')
    ->setReceiverStoreID('131386')          // 退回門市代號
    ->setServerReplyURL($config['server_reply_url']);

try {
    $response = $returnUnimart->send();

    echo "=== B2C 7-ELEVEN 逆物流 ===\n";
    echo '回傳代碼：' . $response->getRtnCode() . "\n";
    echo '回傳訊息：' . $response->getRtnMsg() . "\n";

    if ($response->isSuccess()) {
        echo '逆物流交易編號：' . $response->getAllPayLogisticsID() . "\n";
    }
} catch (Exception $e) {
    echo '錯誤：' . $e->getMessage() . "\n";
}

echo "\n";

// ========== B2C 全家逆物流 ==========

$returnFami = $factory->make('cvs.return')
    ->setAllPayLogisticsID('1234567890')   // 原訂單的物流交易編號
    ->useFamiB2C()                          // 全家 B2C
    ->setGoodsAmount(0)
    ->setGoodsName('退貨商品')
    ->setSenderName('買家姓名')
    ->setSenderPhone('0912345678')
    ->setReceiverStoreID('001779')          // 退回門市代號
    ->setServerReplyURL($config['server_reply_url']);

try {
    $response = $returnFami->send();

    echo "=== B2C 全家逆物流 ===\n";
    echo '回傳代碼：' . $response->getRtnCode() . "\n";
    echo '回傳訊息：' . $response->getRtnMsg() . "\n";

    if ($response->isSuccess()) {
        echo '逆物流交易編號：' . $response->getAllPayLogisticsID() . "\n";
    }
} catch (Exception $e) {
    echo '錯誤：' . $e->getMessage() . "\n";
}

echo "\n";

// ========== B2C 萊爾富逆物流 ==========

$returnHilife = $factory->make('cvs.return')
    ->setAllPayLogisticsID('1234567890')   // 原訂單的物流交易編號
    ->useHilifeB2C()                        // 萊爾富 B2C
    ->setGoodsAmount(0)
    ->setGoodsName('退貨商品')
    ->setSenderName('買家姓名')
    ->setSenderPhone('0912345678')
    ->setReceiverStoreID('2001')            // 退回門市代號
    ->setReceiverName('賣家公司')
    ->setReceiverPhone('02-12345678')
    ->setReceiverCellPhone('0987654321')
    ->setReceiverEmail('seller@example.com')
    ->setServerReplyURL($config['server_reply_url']);

try {
    $response = $returnHilife->send();

    echo "=== B2C 萊爾富逆物流 ===\n";
    echo '回傳代碼：' . $response->getRtnCode() . "\n";
    echo '回傳訊息：' . $response->getRtnMsg() . "\n";

    if ($response->isSuccess()) {
        echo '逆物流交易編號：' . $response->getAllPayLogisticsID() . "\n";
    }
} catch (Exception $e) {
    echo '錯誤：' . $e->getMessage() . "\n";
}

echo "\n";

// ========== 黑貓宅配逆物流 ==========

$returnHome = $factory->make('home.return')
    ->setAllPayLogisticsID('1234567890')   // 原訂單的物流交易編號
    ->setGoodsAmount(0)
    ->setGoodsName('退貨商品')
    ->setTemperature(Temperature::ROOM)    // 常溫
    // 寄件人（買家）資訊
    ->setSenderName('買家姓名')
    ->setSenderPhone('02-11111111')
    ->setSenderCellPhone('0912345678')
    ->setSenderZipCode('106')
    ->setSenderAddress('台北市大安區忠孝東路100號')
    // 收件人（賣家）資訊
    ->setReceiverName('賣家公司')
    ->setReceiverPhone('02-22222222')
    ->setReceiverCellPhone('0987654321')
    ->setReceiverZipCode('320')
    ->setReceiverAddress('桃園市中壢區中正路200號')
    ->setReceiverEmail('seller@example.com')
    ->setServerReplyURL($config['server_reply_url']);

try {
    $response = $returnHome->send();

    echo "=== 黑貓宅配逆物流 ===\n";
    echo '回傳代碼：' . $response->getRtnCode() . "\n";
    echo '回傳訊息：' . $response->getRtnMsg() . "\n";

    if ($response->isSuccess()) {
        echo '逆物流交易編號：' . $response->getAllPayLogisticsID() . "\n";
        echo '貨運單號：' . $response->getBookingNote() . "\n";
    }
} catch (Exception $e) {
    echo '錯誤：' . $e->getMessage() . "\n";
}

