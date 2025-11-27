<?php

/**
 * 列印託運單範例。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\EcPayLogistics\Factories\OperationFactory;
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;

// 載入設定
$config = require __DIR__ . '/_config.php';

// ========== B2C/宅配列印 ==========

$factoryB2C = new OperationFactory([
    'merchant_id' => $config['b2c']['merchant_id'],
    'hash_key' => $config['b2c']['hash_key'],
    'hash_iv' => $config['b2c']['hash_iv'],
    'server_url' => $config['server'],
]);

// 列印 B2C 託運單
$printB2C = $factoryB2C->make('printing.trade')
    ->setAllPayLogisticsID('1234567890')  // 替換成實際的物流交易編號
    ->setLogisticsSubType(LogisticsSubType::FAMI);  // 全家 B2C

try {
    $responseB2C = $printB2C->send();

    echo "=== B2C 列印託運單 ===\n";
    echo '回傳代碼：' . $responseB2C->getRtnCode() . "\n";

    // B2C 列印會直接回傳 HTML 或 PDF
    // 可將回應內容輸出至瀏覽器
    // echo $responseB2C->getRawBody();
} catch (Exception $e) {
    echo '錯誤：' . $e->getMessage() . "\n";
}

echo "\n";

// ========== C2C 列印 ==========

$factoryC2C = new OperationFactory([
    'merchant_id' => $config['c2c']['merchant_id'],
    'hash_key' => $config['c2c']['hash_key'],
    'hash_iv' => $config['c2c']['hash_iv'],
    'server_url' => $config['server'],
]);

// 列印 7-ELEVEN C2C 託運單（需要 CVSPaymentNo 和 CVSValidationNo）
$printC2C = $factoryC2C->make('printing.cvs')
    ->useUnimartC2C()
    ->setCVSPaymentNo('ABC123456')       // 替換成實際的出貨單號
    ->setCVSValidationNo('1234');        // 替換成實際的驗證碼

try {
    $responseC2C = $printC2C->send();

    echo "=== C2C 列印託運單 ===\n";
    echo '回傳代碼：' . $responseC2C->getRtnCode() . "\n";
} catch (Exception $e) {
    echo '錯誤：' . $e->getMessage() . "\n";
}

// 列印全家 C2C 託運單（使用 AllPayLogisticsID）
$printFamiC2C = $factoryC2C->make('printing.cvs')
    ->useFamiC2C()
    ->setAllPayLogisticsID('1234567890');  // 替換成實際的物流交易編號

try {
    $responseFami = $printFamiC2C->send();

    echo "=== 全家 C2C 列印託運單 ===\n";
    echo '回傳代碼：' . $responseFami->getRtnCode() . "\n";
} catch (Exception $e) {
    echo '錯誤：' . $e->getMessage() . "\n";
}
