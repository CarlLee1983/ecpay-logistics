# ECPay Logistics SDK

綠界科技物流整合 PHP SDK

## 安裝

```bash
composer require carllee1983/ecpay-logistics
```

## 環境需求

- PHP 8.3+
- OpenSSL 擴展
- JSON 擴展
- TLS 1.2 支援（綠界僅支援 TLS 1.2 加密通訊協定）

## 重要注意事項

### 安全性警告

- **請勿將 HashKey/HashIV 存放或顯示於前端網頁**（如 JavaScript、HTML、CSS），避免金鑰被盜取造成損失及資料外洩
- 務必透過環境變數或設定檔管理金鑰，確保不納入版本控制

### API 呼叫注意事項

- 所有 API 使用 **HTTP POST** 方式傳送
- 資料傳遞格式採用 **Form-data** 及 **MD5** 加密機制
- 請進行主機**時間校正**，避免時差導致 API 無法正常運作

## 支援的物流類型

### 超商物流

| 類型 | 說明 | 超商 |
|------|------|------|
| C2C | 店到店 | 7-ELEVEN、全家、萊爾富、OK超商 |
| B2C | 大宗寄倉 | 7-ELEVEN、全家、萊爾富 |

### 宅配服務

| 類型 | 說明 |
|------|------|
| TCAT | 黑貓宅急便 |
| POST | 中華郵政 |

## 操作類別對應

### 門市相關 (StoreMap)

| 類別 | 別名 | 說明 |
|------|------|------|
| `OpenStoreMap` | `store_map` | 門市電子地圖 |

### 超商物流 (Cvs)

| 類別 | 別名 | 說明 |
|------|------|------|
| `CreateCvsOrder` | `cvs.create` | 建立超商訂單 |
| `UpdateCvsOrder` | `cvs.update` | 異動超商訂單 |
| `CancelCvsOrder` | `cvs.cancel` | 取消訂單（C2C 7-11） |
| `ReturnCvsOrder` | `cvs.return` | B2C 逆物流 |

### 宅配物流 (Home)

| 類別 | 別名 | 說明 |
|------|------|------|
| `CreateHomeOrder` | `home.create` | 建立宅配訂單 |
| `ReturnHomeOrder` | `home.return` | 宅配逆物流 |

### 查詢 (Queries)

| 類別 | 別名 | 說明 |
|------|------|------|
| `QueryLogisticsOrder` | `queries.order` | 查詢物流訂單 |
| `GetStoreList` | `queries.store_list` | 取得門市清單 |

### 列印 (Printing)

| 類別 | 別名 | 說明 |
|------|------|------|
| `PrintTradeDocument` | `printing.trade` | B2C/宅配列印 |
| `PrintCvsDocument` | `printing.cvs` | C2C 列印 |

### 通知處理 (Notifications)

| 類別 | 說明 |
|------|------|
| `LogisticsNotify` | 物流狀態通知 |
| `ReverseLogisticsNotify` | 逆物流狀態通知 |

## 設定

### 環境變數

```env
ECPAY_LOGISTICS_SERVER=https://logistics-stage.ecpay.com.tw
ECPAY_LOGISTICS_MERCHANT_ID=your_merchant_id
ECPAY_LOGISTICS_HASH_KEY=your_hash_key
ECPAY_LOGISTICS_HASH_IV=your_hash_iv
```

### Laravel 整合

發布設定檔：

```bash
php artisan vendor:publish --provider="CarlLee\EcPayLogistics\Laravel\EcPayLogisticsServiceProvider"
```

## 基本用法

### 門市電子地圖

讓消費者選擇取貨門市：

```php
use CarlLee\EcPayLogistics\Factories\OperationFactory;
use CarlLee\EcPayLogistics\FormBuilder;
use CarlLee\EcPayLogistics\Parameter\LogisticsType;
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;

$factory = new OperationFactory([
    'merchant_id' => 'your_merchant_id',
    'hash_key' => 'your_hash_key',
    'hash_iv' => 'your_hash_iv',
]);

$storeMap = $factory->make('store_map')
    ->setMerchantTradeNo('ORDER_' . time())
    ->setLogisticsType(LogisticsType::CVS)
    ->setLogisticsSubType(LogisticsSubType::UNIMART_C2C)
    ->setIsCollection('N')
    ->setServerReplyURL('https://your-domain.com/store-callback');

$formBuilder = new FormBuilder('https://logistics-stage.ecpay.com.tw');
echo $formBuilder->autoSubmit($storeMap);
```

### 建立超商物流訂單

```php
$order = $factory->make('cvs.create')
    ->setMerchantTradeNo('ORDER_' . time())
    ->setMerchantTradeDate(date('Y/m/d H:i:s'))
    ->setLogisticsType(LogisticsType::CVS)
    ->setLogisticsSubType(LogisticsSubType::UNIMART_C2C)
    ->setGoodsAmount(1000)
    ->setGoodsName('測試商品')
    ->setSenderName('寄件人')
    ->setSenderCellPhone('0912345678')
    ->setReceiverName('收件人')
    ->setReceiverCellPhone('0987654321')
    ->setReceiverStoreID('991182')  // 門市代號
    ->setServerReplyURL('https://your-domain.com/logistics-callback');

$response = $order->send();
```

### 建立宅配訂單

```php
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;
use CarlLee\EcPayLogistics\Parameter\Temperature;
use CarlLee\EcPayLogistics\Parameter\Distance;
use CarlLee\EcPayLogistics\Parameter\Specification;

$order = $factory->make('home.create')
    ->setMerchantTradeNo('HOME_' . time())
    ->setLogisticsType(LogisticsType::HOME)
    ->setLogisticsSubType(LogisticsSubType::TCAT)
    ->setGoodsAmount(2000)
    ->setGoodsName('測試商品')
    ->setSenderName('寄件人')
    ->setSenderPhone('02-12345678')
    ->setSenderCellPhone('0912345678')
    ->setSenderZipCode('106')
    ->setSenderAddress('台北市大安區忠孝東路100號')
    ->setReceiverName('收件人')
    ->setReceiverPhone('03-12345678')
    ->setReceiverCellPhone('0987654321')
    ->setReceiverZipCode('320')
    ->setReceiverAddress('桃園市中壢區中正路200號')
    ->setTemperature(Temperature::ROOM)
    ->setDistance(Distance::SAME)
    ->setSpecification(Specification::SIZE_60)
    ->setServerReplyURL('https://your-domain.com/logistics-callback');

$response = $order->send();
```

### 處理物流狀態通知

```php
use CarlLee\EcPayLogistics\Notifications\LogisticsNotify;

$notify = new LogisticsNotify($hashKey, $hashIV);

if ($notify->verify($_POST)) {
    $logisticsId = $notify->getAllPayLogisticsID();
    $status = $notify->getRtnCode();
    
    // 更新訂單物流狀態
    
    // 重要：必須回傳 1|OK 給綠界
    echo $notify->getSuccessResponse(); // 1|OK
}
```

### 查詢物流訂單

```php
$query = $factory->make('queries.order')
    ->setAllPayLogisticsID('1234567890');

$response = $query->send();
```

## 前端框架整合（Vue / React / Next.js）

由於綠界物流的門市電子地圖需要透過表單 POST 提交，在現代前端框架（SPA）中需要特別處理。以下提供幾種整合方式：

### 後端 API 設計

首先，建立後端 API 端點來產生表單資料：

```php
// Laravel 範例：routes/api.php
Route::post('/logistics/store-map', function (Request $request) {
    $factory = app('ecpay.logistics');
    
    $storeMap = $factory->make('store_map')
        ->setMerchantTradeNo('ORDER_' . time())
        ->setLogisticsSubType(LogisticsSubType::from($request->input('sub_type', 'UNIMARTC2C')))
        ->setIsCollection($request->input('is_collection', 'N'))
        ->setServerReplyURL(config('app.url') . '/api/logistics/store-callback');
    
    $formBuilder = new FormBuilder(config('ecpay-logistics.server'));
    
    return response()->json([
        'action' => $formBuilder->getActionUrl($storeMap),
        'fields' => $formBuilder->getFields($storeMap),
    ]);
});

// 門市選擇回調（綠界會 POST 到此端點）
Route::post('/logistics/store-callback', function (Request $request) {
    // 儲存門市資訊到 Session 或快取
    $storeData = [
        'CVSStoreID' => $request->input('CVSStoreID'),
        'CVSStoreName' => $request->input('CVSStoreName'),
        'CVSAddress' => $request->input('CVSAddress'),
        'CVSOutSide' => $request->input('CVSOutSide'),
        'ExtraData' => $request->input('ExtraData'),
    ];
    
    // 儲存到快取（以 MerchantTradeNo 為 key）
    $tradeNo = $request->input('MerchantTradeNo');
    cache()->put("store_selection_{$tradeNo}", $storeData, now()->addHours(1));
    
    // 回傳 HTML 頁面，通知父視窗關閉
    return response()->view('logistics.store-callback', ['store' => $storeData]);
});
```

### 方式一：彈出視窗（Popup）— 推薦

**優點**：不影響主頁面狀態，使用者體驗較佳

#### Vue 3 (Composition API)

```vue
<template>
  <div class="store-selector">
    <button @click="openStoreMap" :disabled="loading" class="btn-select-store">
      {{ loading ? '載入中...' : '選擇門市' }}
    </button>
    
    <div v-if="selectedStore" class="store-info">
      <p><strong>門市名稱：</strong>{{ selectedStore.CVSStoreName }}</p>
      <p><strong>門市代號：</strong>{{ selectedStore.CVSStoreID }}</p>
      <p><strong>門市地址：</strong>{{ selectedStore.CVSAddress }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  subType: { type: String, default: 'UNIMARTC2C' },
  isCollection: { type: String, default: 'N' }
})

const emit = defineEmits(['store-selected'])

const loading = ref(false)
const selectedStore = ref(null)
let popup = null
let checkInterval = null

// 監聽來自回調頁面的訊息
const handleMessage = (event) => {
  // 驗證來源（請替換為你的網域）
  if (event.origin !== window.location.origin) return
  
  if (event.data?.type === 'STORE_SELECTED') {
    selectedStore.value = event.data.store
    emit('store-selected', event.data.store)
    
    if (popup && !popup.closed) {
      popup.close()
    }
  }
}

const openStoreMap = async () => {
  loading.value = true
  
  try {
    // 從後端取得表單資料
    const response = await fetch('/api/logistics/store-map', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
      },
      body: JSON.stringify({
        sub_type: props.subType,
        is_collection: props.isCollection
      })
    })
    
    const { action, fields } = await response.json()
    
    // 開啟彈出視窗
    const width = 800
    const height = 600
    const left = (screen.width - width) / 2
    const top = (screen.height - height) / 2
    
    popup = window.open('', 'ECPayStoreMap', 
      `width=${width},height=${height},left=${left},top=${top},scrollbars=yes`)
    
    // 建立並提交表單到彈出視窗
    const form = document.createElement('form')
    form.method = 'POST'
    form.action = action
    form.target = 'ECPayStoreMap'
    
    Object.entries(fields).forEach(([name, value]) => {
      const input = document.createElement('input')
      input.type = 'hidden'
      input.name = name
      input.value = value
      form.appendChild(input)
    })
    
    document.body.appendChild(form)
    form.submit()
    document.body.removeChild(form)
    
    // 檢查彈出視窗是否關閉
    checkInterval = setInterval(() => {
      if (popup && popup.closed) {
        clearInterval(checkInterval)
        loading.value = false
      }
    }, 500)
    
  } catch (error) {
    console.error('開啟門市地圖失敗:', error)
    alert('開啟門市地圖失敗，請稍後再試')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  window.addEventListener('message', handleMessage)
})

onUnmounted(() => {
  window.removeEventListener('message', handleMessage)
  if (checkInterval) clearInterval(checkInterval)
})
</script>
```

#### React (Hooks)

```jsx
import { useState, useEffect, useCallback } from 'react'

function StoreSelector({ subType = 'UNIMARTC2C', isCollection = 'N', onStoreSelected }) {
  const [loading, setLoading] = useState(false)
  const [selectedStore, setSelectedStore] = useState(null)
  
  // 監聽來自回調頁面的訊息
  useEffect(() => {
    const handleMessage = (event) => {
      if (event.origin !== window.location.origin) return
      
      if (event.data?.type === 'STORE_SELECTED') {
        setSelectedStore(event.data.store)
        onStoreSelected?.(event.data.store)
      }
    }
    
    window.addEventListener('message', handleMessage)
    return () => window.removeEventListener('message', handleMessage)
  }, [onStoreSelected])
  
  const openStoreMap = useCallback(async () => {
    setLoading(true)
    
    try {
      const response = await fetch('/api/logistics/store-map', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ sub_type: subType, is_collection: isCollection })
      })
      
      const { action, fields } = await response.json()
      
      // 開啟彈出視窗
      const width = 800, height = 600
      const left = (screen.width - width) / 2
      const top = (screen.height - height) / 2
      
      const popup = window.open('', 'ECPayStoreMap',
        `width=${width},height=${height},left=${left},top=${top},scrollbars=yes`)
      
      // 建立表單並提交
      const form = document.createElement('form')
      form.method = 'POST'
      form.action = action
      form.target = 'ECPayStoreMap'
      
      Object.entries(fields).forEach(([name, value]) => {
        const input = document.createElement('input')
        input.type = 'hidden'
        input.name = name
        input.value = value
        form.appendChild(input)
      })
      
      document.body.appendChild(form)
      form.submit()
      document.body.removeChild(form)
      
      // 監聯視窗關閉
      const checkClosed = setInterval(() => {
        if (popup?.closed) {
          clearInterval(checkClosed)
          setLoading(false)
        }
      }, 500)
      
    } catch (error) {
      console.error('開啟門市地圖失敗:', error)
      alert('開啟門市地圖失敗')
    } finally {
      setLoading(false)
    }
  }, [subType, isCollection])
  
  return (
    <div className="store-selector">
      <button onClick={openStoreMap} disabled={loading}>
        {loading ? '載入中...' : '選擇門市'}
      </button>
      
      {selectedStore && (
        <div className="store-info">
          <p><strong>門市名稱：</strong>{selectedStore.CVSStoreName}</p>
          <p><strong>門市代號：</strong>{selectedStore.CVSStoreID}</p>
          <p><strong>門市地址：</strong>{selectedStore.CVSAddress}</p>
        </div>
      )}
    </div>
  )
}

export default StoreSelector
```

### 回調頁面模板

建立 `resources/views/logistics/store-callback.blade.php`（Laravel Blade）：

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>門市選擇完成</title>
</head>
<body>
    <p>門市選擇完成，視窗即將關閉...</p>
    
    <script>
        // 傳送訊息給父視窗
        if (window.opener) {
            window.opener.postMessage({
                type: 'STORE_SELECTED',
                store: @json($store)
            }, window.location.origin);
            
            // 延遲關閉視窗，確保訊息送達
            setTimeout(() => window.close(), 500);
        } else {
            // 如果是 iframe，傳送給父框架
            window.parent.postMessage({
                type: 'STORE_SELECTED',
                store: @json($store)
            }, window.location.origin);
        }
    </script>
</body>
</html>
```

### 方式二：iframe 嵌入

適用於需要將門市地圖嵌入頁面內的場景：

```vue
<template>
  <div class="store-map-container">
    <iframe
      v-if="iframeSrc"
      ref="mapFrame"
      :src="iframeSrc"
      width="100%"
      height="600"
      frameborder="0"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const iframeSrc = ref('')
const mapFrame = ref(null)

const emit = defineEmits(['store-selected'])

// 使用隱藏表單提交到 iframe
const loadStoreMap = async () => {
  const response = await fetch('/api/logistics/store-map', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ sub_type: 'UNIMARTC2C' })
  })
  
  const { action, fields } = await response.json()
  
  // 建立隱藏 iframe 用於提交
  const iframe = document.createElement('iframe')
  iframe.name = 'storeMapFrame'
  iframe.style.width = '100%'
  iframe.style.height = '600px'
  iframe.style.border = 'none'
  
  const container = document.querySelector('.store-map-container')
  container.appendChild(iframe)
  
  // 建立表單提交到 iframe
  const form = document.createElement('form')
  form.method = 'POST'
  form.action = action
  form.target = 'storeMapFrame'
  
  Object.entries(fields).forEach(([name, value]) => {
    const input = document.createElement('input')
    input.type = 'hidden'
    input.name = name
    input.value = value
    form.appendChild(input)
  })
  
  document.body.appendChild(form)
  form.submit()
  document.body.removeChild(form)
}

const handleMessage = (event) => {
  if (event.data?.type === 'STORE_SELECTED') {
    emit('store-selected', event.data.store)
  }
}

onMounted(() => {
  window.addEventListener('message', handleMessage)
  loadStoreMap()
})

onUnmounted(() => {
  window.removeEventListener('message', handleMessage)
})
</script>
```

### 方式三：新分頁導向（傳統方式）

適用於不需要保持 SPA 狀態的場景：

```php
// 後端：儲存當前頁面 URL，供回調後重導
Route::post('/logistics/store-map-redirect', function (Request $request) {
    $factory = app('ecpay.logistics');
    
    // 儲存回調後要返回的頁面
    session(['return_url' => $request->input('return_url', '/')]);
    
    $storeMap = $factory->make('store_map')
        ->setMerchantTradeNo('ORDER_' . time())
        ->setLogisticsSubType(LogisticsSubType::UNIMART_C2C)
        ->setServerReplyURL(route('logistics.store-callback'));
    
    $formBuilder = new FormBuilder(config('ecpay-logistics.server'));
    
    // 直接輸出自動提交表單
    return response($formBuilder->autoSubmit($storeMap));
});

// 回調處理：重導回前端頁面
Route::post('/logistics/store-callback', function (Request $request) {
    $returnUrl = session('return_url', '/');
    
    // 將門市資訊附加到 URL query string
    $storeData = [
        'store_id' => $request->input('CVSStoreID'),
        'store_name' => $request->input('CVSStoreName'),
        'store_address' => $request->input('CVSAddress'),
    ];
    
    $redirectUrl = $returnUrl . '?' . http_build_query(['store' => $storeData]);
    
    return redirect($redirectUrl);
});
```

```jsx
// React: 從 URL 讀取門市資訊
import { useSearchParams } from 'react-router-dom'

function CheckoutPage() {
  const [searchParams] = useSearchParams()
  const storeData = searchParams.get('store')
  
  const selectedStore = storeData ? JSON.parse(storeData) : null
  
  const openStoreMap = () => {
    // 建立表單導向到後端
    const form = document.createElement('form')
    form.method = 'POST'
    form.action = '/logistics/store-map-redirect'
    
    const input = document.createElement('input')
    input.type = 'hidden'
    input.name = 'return_url'
    input.value = window.location.pathname
    form.appendChild(input)
    
    // CSRF token
    const csrf = document.createElement('input')
    csrf.type = 'hidden'
    csrf.name = '_token'
    csrf.value = document.querySelector('meta[name="csrf-token"]')?.content
    form.appendChild(csrf)
    
    document.body.appendChild(form)
    form.submit()
  }
  
  return (
    <div>
      <button onClick={openStoreMap}>選擇取貨門市</button>
      {selectedStore && <p>已選擇：{selectedStore.store_name}</p>}
    </div>
  )
}
```

### Next.js (App Router) 整合

```tsx
// app/api/logistics/store-map/route.ts
import { NextRequest, NextResponse } from 'next/server'

export async function POST(request: NextRequest) {
  const body = await request.json()
  
  // 呼叫 PHP 後端 API 或直接實作
  const response = await fetch(`${process.env.PHP_API_URL}/logistics/store-map`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(body)
  })
  
  return NextResponse.json(await response.json())
}
```

```tsx
// components/StoreSelector.tsx
'use client'

import { useState, useEffect } from 'react'

interface Store {
  CVSStoreID: string
  CVSStoreName: string
  CVSAddress: string
}

export default function StoreSelector({ 
  onSelect 
}: { 
  onSelect: (store: Store) => void 
}) {
  const [loading, setLoading] = useState(false)
  
  useEffect(() => {
    const handleMessage = (event: MessageEvent) => {
      if (event.data?.type === 'STORE_SELECTED') {
        onSelect(event.data.store)
      }
    }
    
    window.addEventListener('message', handleMessage)
    return () => window.removeEventListener('message', handleMessage)
  }, [onSelect])
  
  const openStoreMap = async () => {
    setLoading(true)
    
    try {
      const res = await fetch('/api/logistics/store-map', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ sub_type: 'UNIMARTC2C' })
      })
      
      const { action, fields } = await res.json()
      
      const popup = window.open('', 'ECPayStoreMap', 
        'width=800,height=600,scrollbars=yes')
      
      const form = document.createElement('form')
      form.method = 'POST'
      form.action = action
      form.target = 'ECPayStoreMap'
      
      Object.entries(fields as Record<string, string>).forEach(([name, value]) => {
        const input = document.createElement('input')
        input.type = 'hidden'
        input.name = name
        input.value = value
        form.appendChild(input)
      })
      
      document.body.appendChild(form)
      form.submit()
      document.body.removeChild(form)
      
    } finally {
      setLoading(false)
    }
  }
  
  return (
    <button onClick={openStoreMap} disabled={loading}>
      {loading ? '載入中...' : '選擇門市'}
    </button>
  )
}
```

### 注意事項

1. **跨域問題**：確保 `postMessage` 的 `origin` 驗證正確設定
2. **CSRF 保護**：API 請求需要包含 CSRF token
3. **彈出視窗阻擋**：部分瀏覽器可能阻擋彈出視窗，建議在使用者點擊事件中觸發
4. **行動裝置**：彈出視窗在行動裝置上體驗不佳，建議使用新分頁或 iframe 方式
5. **狀態保存**：SPA 在導向回來時可能丟失狀態，使用 `localStorage` 或後端 Session 保存

## 測試環境資訊

| 項目 | C2C 測試 | B2C 測試 |
|------|----------|----------|
| 測試環境網址 | https://logistics-stage.ecpay.com.tw | https://logistics-stage.ecpay.com.tw |
| 特店編號 | 2000132 | 2000933 |
| HashKey | 5294y06JbISpM5x9 | XBERn1YOvpM9nfZc |
| HashIV | v77hoKGq4kWxNNIS | h1ONHk4P4yqbl5LK |

> 參考：[綠界物流整合 API 技術文件](https://developers.ecpay.com.tw/?p=7380)

## 相關資源

- [綠界物流整合 API 技術文件](https://developers.ecpay.com.tw/?p=7380)
- [綠界特店管理後台（測試）](https://vendor-stage.ecpay.com.tw/)

## 授權

MIT License

