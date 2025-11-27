# Laravel 整合使用範例

## 安裝設定

### 1. 安裝套件

```bash
composer require carllee1983/ecpay-logistics
```

### 2. 發布設定檔

```bash
php artisan vendor:publish --provider="CarlLee\EcPayLogistics\Laravel\EcPayLogisticsServiceProvider"
```

### 3. 設定環境變數

在 `.env` 檔案中加入：

```env
ECPAY_LOGISTICS_SERVER=https://logistics-stage.ecpay.com.tw
ECPAY_LOGISTICS_MERCHANT_ID=2000132
ECPAY_LOGISTICS_HASH_KEY=5294y06JbISpM5x9
ECPAY_LOGISTICS_HASH_IV=v77hoKGq4kWxNNIS
ECPAY_LOGISTICS_SERVER_REPLY_URL=https://your-domain.com/logistics/callback
```

## 使用範例

### 使用 Facade

```php
use CarlLee\EcPayLogistics\Laravel\Facades\EcPayLogistics;
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;

// 開啟門市電子地圖
public function selectStore()
{
    $formData = EcPayLogistics::openStoreMap(
        'ORDER_' . time(),
        LogisticsSubType::UNIMART_C2C,
        false,
        route('logistics.store-callback')
    );

    return response()->json($formData);
}

// 建立超商訂單
public function createCvsOrder(Request $request)
{
    $response = EcPayLogistics::createCvsOrder([
        'MerchantTradeNo' => 'ORDER_' . time(),
        'LogisticsSubType' => LogisticsSubType::UNIMART_C2C,
        'GoodsAmount' => 100,
        'GoodsName' => '測試商品',
        'SenderName' => '測試寄件人',
        'SenderCellPhone' => '0912345678',
        'ReceiverName' => '測試收件人',
        'ReceiverCellPhone' => '0987654321',
        'ReceiverStoreID' => $request->store_id,
        'ServerReplyURL' => route('logistics.callback'),
    ]);

    if ($response->isSuccess()) {
        return response()->json([
            'success' => true,
            'logistics_id' => $response->getAllPayLogisticsID(),
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => $response->getRtnMsg(),
    ], 400);
}

// 查詢訂單
public function queryOrder($logisticsId)
{
    $response = EcPayLogistics::queryOrder($logisticsId);

    return response()->json($response->getData());
}
```

### 使用依賴注入

```php
use CarlLee\EcPayLogistics\Factories\OperationFactoryInterface;
use CarlLee\EcPayLogistics\FormBuilder;

class LogisticsController extends Controller
{
    public function __construct(
        protected OperationFactoryInterface $factory,
        protected FormBuilder $formBuilder
    ) {}

    public function storeMap()
    {
        $storeMap = $this->factory->make('store_map')
            ->setMerchantTradeNo('ORDER_' . time())
            ->useUnimartC2C()
            ->withoutCollection()
            ->setServerReplyURL(route('logistics.store-callback'));

        return response($this->formBuilder->autoSubmit($storeMap));
    }

    public function createOrder(Request $request)
    {
        $order = $this->factory->make('cvs.create')
            ->setMerchantTradeNo($request->order_no)
            ->useUnimartC2C()
            ->setGoodsAmount($request->amount)
            ->setGoodsName($request->goods_name)
            ->setSenderName($request->sender_name)
            ->setSenderCellPhone($request->sender_phone)
            ->setReceiverName($request->receiver_name)
            ->setReceiverCellPhone($request->receiver_phone)
            ->setReceiverStoreID($request->store_id)
            ->setServerReplyURL(route('logistics.callback'));

        $response = $order->send();

        return response()->json([
            'success' => $response->isSuccess(),
            'data' => $response->getData(),
        ]);
    }
}
```

### 處理物流通知

```php
use CarlLee\EcPayLogistics\Notifications\LogisticsNotify;
use Illuminate\Http\Request;

class LogisticsCallbackController extends Controller
{
    public function handle(Request $request)
    {
        $notify = new LogisticsNotify(
            config('ecpay-logistics.hash_key'),
            config('ecpay-logistics.hash_iv')
        );

        if ($notify->verify($request->all())) {
            // 更新訂單狀態
            $order = Order::where('merchant_trade_no', $notify->getMerchantTradeNo())->first();
            
            if ($order) {
                $order->logistics_id = $notify->getAllPayLogisticsID();
                $order->logistics_status = $notify->getRtnCode();
                $order->logistics_message = $notify->getRtnMsg();
                $order->save();
            }

            // 回傳成功
            return response($notify->getSuccessResponse());
        }

        return response('驗證失敗', 400);
    }
}
```

### 路由設定

```php
// routes/web.php
Route::prefix('logistics')->group(function () {
    Route::get('store-map', [LogisticsController::class, 'storeMap']);
    Route::post('store-callback', [LogisticsController::class, 'storeCallback'])->name('logistics.store-callback');
    Route::post('callback', [LogisticsCallbackController::class, 'handle'])->name('logistics.callback');
});
```

## 注意事項

1. 請確保 `ServerReplyURL` 是可公開存取的 HTTPS 網址
2. 回呼路由請排除 CSRF 驗證：

```php
// app/Http/Middleware/VerifyCsrfToken.php
protected $except = [
    'logistics/callback',
    'logistics/store-callback',
];
```
