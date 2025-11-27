<?php

/**
 * 門市電子地圖回呼範例。
 *
 * 當消費者在綠界的門市電子地圖選擇門市後，綠界會將選擇結果 POST 到此網址。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\EcPayLogistics\Infrastructure\CheckMacEncoder;

// 載入設定
$config = require __DIR__ . '/_config.php';

// 建立編碼器
$encoder = new CheckMacEncoder(
    $config['c2c']['hash_key'],
    $config['c2c']['hash_iv']
);

// 驗證回傳資料
if ($encoder->verifyResponse($_POST)) {
    // 取得門市資訊
    $merchantTradeNo = $_POST['MerchantTradeNo'] ?? '';
    $logisticsSubType = $_POST['LogisticsSubType'] ?? '';
    $cvsStoreId = $_POST['CVSStoreID'] ?? '';
    $cvsStoreName = $_POST['CVSStoreName'] ?? '';
    $cvsAddress = $_POST['CVSAddress'] ?? '';
    $cvsTelephone = $_POST['CVSTelephone'] ?? '';
    $extraData = $_POST['ExtraData'] ?? '';

    // 記錄選擇的門市資訊（實際應用中請存入資料庫或 Session）
    $log = sprintf(
        "[%s] 門市選擇結果\n" .
        "特店交易編號：%s\n" .
        "物流子類型：%s\n" .
        "門市代號：%s\n" .
        "門市名稱：%s\n" .
        "門市地址：%s\n" .
        "門市電話：%s\n" .
        "額外資訊：%s\n" .
        "---\n",
        date('Y-m-d H:i:s'),
        $merchantTradeNo,
        $logisticsSubType,
        $cvsStoreId,
        $cvsStoreName,
        $cvsAddress,
        $cvsTelephone,
        $extraData
    );

    file_put_contents(__DIR__ . '/store_selection.log', $log, FILE_APPEND);

    // TODO: 在此處理門市選擇結果
    // 例如：將門市資訊存入 Session 或資料庫
    // session_start();
    // $_SESSION['selected_store'] = [
    //     'store_id' => $cvsStoreId,
    //     'store_name' => $cvsStoreName,
    //     'store_address' => $cvsAddress,
    // ];

    // 導回前端頁面
    header('Location: https://your-domain.com/checkout?store_selected=1');
    exit;
} else {
    // 驗證失敗
    http_response_code(400);
    echo '驗證失敗';
}
