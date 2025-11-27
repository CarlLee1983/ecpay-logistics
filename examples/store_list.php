<?php

/**
 * 取得門市清單範例。
 *
 * 此範例展示如何透過 API 查詢超商門市資訊。
 * 與電子地圖不同，此 API 直接回傳門市資料，不需要使用者介面。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\EcPayLogistics\Factories\OperationFactory;
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;
use CarlLee\EcPayLogistics\Parameter\StoreType;

// 載入設定
$config = require __DIR__ . '/_config.php';

// 建立工廠
$factory = new OperationFactory([
    'merchant_id' => $config['c2c']['merchant_id'],
    'hash_key' => $config['c2c']['hash_key'],
    'hash_iv' => $config['c2c']['hash_iv'],
    'server_url' => $config['server'],
]);

// ========== 依關鍵字搜尋門市 ==========

$storeListByKeyword = $factory->make('queries.store_list')
    ->searchUnimart()                          // 搜尋 7-ELEVEN C2C 門市
    ->byKeyword('忠孝');                        // 以關鍵字搜尋

try {
    $response = $storeListByKeyword->send();

    echo "=== 依關鍵字搜尋 7-ELEVEN 門市 ===\n";
    echo '回傳代碼：' . $response->getRtnCode() . "\n";

    if ($response->isSuccess()) {
        $stores = $response->getData();
        echo "找到 " . count($stores) . " 家門市\n\n";

        // 顯示前 5 筆
        foreach (array_slice($stores, 0, 5) as $store) {
            echo "門市代號：" . ($store['StoreID'] ?? $store['CVSStoreID'] ?? '') . "\n";
            echo "門市名稱：" . ($store['StoreName'] ?? $store['CVSStoreName'] ?? '') . "\n";
            echo "門市地址：" . ($store['Address'] ?? $store['CVSAddress'] ?? '') . "\n";
            echo "---\n";
        }
    }
} catch (Exception $e) {
    echo '錯誤：' . $e->getMessage() . "\n";
}

echo "\n";

// ========== 依郵遞區號搜尋門市 ==========

$storeListByZip = $factory->make('queries.store_list')
    ->searchFami()                             // 搜尋全家 C2C 門市
    ->byZipCode('106');                        // 以郵遞區號搜尋

try {
    $response = $storeListByZip->send();

    echo "=== 依郵遞區號搜尋全家門市（106 大安區）===\n";
    echo '回傳代碼：' . $response->getRtnCode() . "\n";

    if ($response->isSuccess()) {
        $stores = $response->getData();
        echo "找到 " . count($stores) . " 家門市\n";
    }
} catch (Exception $e) {
    echo '錯誤：' . $e->getMessage() . "\n";
}

echo "\n";

// ========== 依縣市搜尋門市 ==========

$storeListByCity = $factory->make('queries.store_list')
    ->searchHilife()                           // 搜尋萊爾富 C2C 門市
    ->byCity('台北市');                         // 以縣市搜尋

try {
    $response = $storeListByCity->send();

    echo "=== 依縣市搜尋萊爾富門市（台北市）===\n";
    echo '回傳代碼：' . $response->getRtnCode() . "\n";

    if ($response->isSuccess()) {
        $stores = $response->getData();
        echo "找到 " . count($stores) . " 家門市\n";
    }
} catch (Exception $e) {
    echo '錯誤：' . $e->getMessage() . "\n";
}

echo "\n";

// ========== 搜尋可退貨門市 ==========

$storeListReturn = $factory->make('queries.store_list')
    ->searchUnimart(false)                     // 搜尋 7-ELEVEN B2C 門市
    ->returnOnly()                             // 僅搜尋退貨店
    ->byZipCode('320');                        // 桃園市中壢區

try {
    $response = $storeListReturn->send();

    echo "=== 搜尋 7-ELEVEN B2C 可退貨門市（320 中壢區）===\n";
    echo '回傳代碼：' . $response->getRtnCode() . "\n";

    if ($response->isSuccess()) {
        $stores = $response->getData();
        echo "找到 " . count($stores) . " 家可退貨門市\n";
    }
} catch (Exception $e) {
    echo '錯誤：' . $e->getMessage() . "\n";
}

echo "\n";

// ========== 使用傳統方式設定參數 ==========

$storeListTraditional = $factory->make('queries.store_list')
    ->setLogisticsSubType(LogisticsSubType::OKMART_C2C)
    ->setStoreType(StoreType::PICKUP_ONLY)
    ->setKeyword('中正路');

try {
    $response = $storeListTraditional->send();

    echo "=== 搜尋 OK超商門市（傳統方式）===\n";
    echo '回傳代碼：' . $response->getRtnCode() . "\n";

    if ($response->isSuccess()) {
        $stores = $response->getData();
        echo "找到 " . count($stores) . " 家門市\n";
    }
} catch (Exception $e) {
    echo '錯誤：' . $e->getMessage() . "\n";
}

