# Changelog

本專案的所有重要變更都會記錄在此文件中。

格式基於 [Keep a Changelog](https://keepachangelog.com/zh-TW/1.0.0/)，
並且本專案遵循 [語義化版本](https://semver.org/lang/zh-TW/)。

## [1.0.0] - 2025-11-27

### 新增

#### 核心功能
- 完整的綠界物流 API 整合
- CheckMacValue MD5 加密驗證機制
- PSR-3 日誌支援
- HTTP 請求重試機制（指數退避）
- Laravel 11 整合支援（Service Provider、Facade）

#### 超商物流 (CVS)
- `CreateCvsOrder` - C2C/B2C 超商訂單建立
- `UpdateCvsOrder` - 異動超商訂單（7-11 C2C、全家 B2C）
- `CancelCvsOrder` - 取消訂單（7-11 C2C）
- `ReturnCvsOrder` - B2C 逆物流申請

#### 宅配物流 (Home)
- `CreateHomeOrder` - 宅配訂單建立（黑貓、郵局）
- `ReturnHomeOrder` - 宅配逆物流申請（黑貓）

#### 門市地圖
- `OpenStoreMap` - 門市電子地圖整合
- `GetStoreList` - 門市清單查詢 API

#### 查詢與列印
- `QueryLogisticsOrder` - 物流訂單狀態查詢
- `PrintTradeDocument` - B2C/宅配託運單列印
- `PrintCvsDocument` - C2C 超商單據列印

#### 通知處理
- `LogisticsNotify` - 物流狀態通知處理
- `ReverseLogisticsNotify` - 逆物流狀態通知處理

#### 參數列舉
- `LogisticsType` - 物流類型（CVS、HOME）
- `LogisticsSubType` - 物流子類型（各超商 C2C/B2C、TCAT、POST）
- `IsCollection` - 是否代收貨款
- `Temperature` - 溫層（常溫、冷藏、冷凍）
- `Distance` - 配送距離
- `Specification` - 包裹規格
- `ScheduledPickupTime` - 預約取件時段
- `ScheduledDeliveryTime` - 預約配達時段
- `Device` - 裝置類型
- `StoreType` - 門市類型

#### 工具類別
- `OperationFactory` - 物流操作工廠
- `FormBuilder` - 表單產生器（支援 CSP Nonce）
- `CheckMacEncoder` - CheckMacValue 編碼器
- `Response` - API 回應封裝

### 安全性
- HashKey/HashIV 空值驗證
- 敏感資料日誌遮蔽
- CSP Nonce 支援
- XSS 防護（HTML 特殊字元轉義）

### 文件
- 完整的 README 使用說明
- 13 個範例程式碼
- 前端框架整合指南（Vue、React、Next.js）
- Laravel 整合說明

### 測試
- 107 個單元測試
- 235 個斷言
- 涵蓋所有核心功能

## 參考資源

- [綠界物流整合 API 技術文件](https://developers.ecpay.com.tw/?p=7380)
- [綠界測試環境](https://logistics-stage.ecpay.com.tw)

[1.0.0]: https://github.com/carllee1983/ecpay-logistics/releases/tag/v1.0.0

