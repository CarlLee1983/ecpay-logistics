<?php

/**
 * 物流狀態通知回呼範例。
 *
 * 此範例展示如何處理綠界回傳的物流狀態通知。
 * 請將此檔案放置於可公開存取的 URL，並設定為 ServerReplyURL。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\EcPayLogistics\Notifications\LogisticsNotify;

// 載入設定
$config = require __DIR__ . '/_config.php';

// 建立通知處理器（依據實際使用的帳號選擇 C2C 或 B2C）
$notify = new LogisticsNotify(
    $config['c2c']['hash_key'],
    $config['c2c']['hash_iv']
);

// 驗證通知資料
if ($notify->verify($_POST)) {
    // 取得通知資料
    $logisticsId = $notify->getAllPayLogisticsID();
    $tradeNo = $notify->getMerchantTradeNo();
    $rtnCode = $notify->getRtnCode();
    $rtnMsg = $notify->getRtnMsg();

    // 記錄通知（實際應用中請存入資料庫）
    $log = sprintf(
        "[%s] 物流狀態通知\n" .
        "物流交易編號：%s\n" .
        "特店交易編號：%s\n" .
        "狀態代碼：%s\n" .
        "狀態訊息：%s\n" .
        "---\n",
        date('Y-m-d H:i:s'),
        $logisticsId,
        $tradeNo,
        $rtnCode,
        $rtnMsg
    );

    // 寫入日誌檔（實際應用中建議使用 Monolog 或其他日誌套件）
    file_put_contents(__DIR__ . '/logistics_notify.log', $log, FILE_APPEND);

    // TODO: 在此更新訂單物流狀態
    // 例如：
    // $order = Order::where('merchant_trade_no', $tradeNo)->first();
    // $order->logistics_status = $rtnCode;
    // $order->logistics_id = $logisticsId;
    // $order->save();

    // 重要：必須回傳 1|OK 給綠界
    echo $notify->getSuccessResponse();
} else {
    // CheckMacValue 驗證失敗
    http_response_code(400);
    echo '驗證失敗';

    // 記錄錯誤
    $log = sprintf(
        "[%s] CheckMacValue 驗證失敗\n" .
        "POST 資料：%s\n" .
        "---\n",
        date('Y-m-d H:i:s'),
        json_encode($_POST, JSON_UNESCAPED_UNICODE)
    );
    file_put_contents(__DIR__ . '/logistics_error.log', $log, FILE_APPEND);
}
