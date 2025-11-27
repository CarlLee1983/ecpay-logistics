<?php

/**
 * 門市電子地圖範例。
 *
 * 此範例展示如何開啟綠界的門市電子地圖，讓消費者選擇取貨門市。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\EcPayLogistics\Factories\OperationFactory;
use CarlLee\EcPayLogistics\FormBuilder;
use CarlLee\EcPayLogistics\Parameter\Device;
use CarlLee\EcPayLogistics\Parameter\IsCollection;
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;

// 載入設定
$config = require __DIR__ . '/_config.php';

// 建立工廠（使用 C2C 測試帳號）
$factory = new OperationFactory([
    'merchant_id' => $config['c2c']['merchant_id'],
    'hash_key' => $config['c2c']['hash_key'],
    'hash_iv' => $config['c2c']['hash_iv'],
    'server_url' => $config['server'],
]);

// 建立門市地圖操作
$storeMap = $factory->make('store_map')
    ->setMerchantTradeNo('TEST_' . time())
    ->setLogisticsSubType(LogisticsSubType::UNIMART_C2C)  // 7-ELEVEN C2C
    ->setIsCollection(IsCollection::NO)                   // 不代收貨款
    ->setDevice(Device::PC)                               // 桌機版
    ->setServerReplyURL($config['server_reply_url']);     // 門市選擇後回傳網址

// 建立表單產生器
$formBuilder = new FormBuilder($config['server']);

// 方式一：產生自動提交的完整 HTML 頁面
echo $formBuilder->autoSubmit($storeMap);

// 方式二：取得表單資料供前端使用
// $formData = $formBuilder->toArray($storeMap);
// header('Content-Type: application/json');
// echo json_encode($formData, JSON_UNESCAPED_UNICODE);
