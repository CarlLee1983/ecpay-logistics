<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Laravel\Services;

use CarlLee\EcPayLogistics\Content;
use CarlLee\EcPayLogistics\Factories\OperationFactoryInterface;
use CarlLee\EcPayLogistics\FormBuilder;
use CarlLee\EcPayLogistics\Operations\Cvs\CreateCvsOrder;
use CarlLee\EcPayLogistics\Operations\Home\CreateHomeOrder;
use CarlLee\EcPayLogistics\Operations\StoreMap\OpenStoreMap;
use CarlLee\EcPayLogistics\Parameter\IsCollection;
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;
use CarlLee\EcPayLogistics\Queries\QueryLogisticsOrder;
use CarlLee\EcPayLogistics\Response;

/**
 * 物流協調器。
 *
 * 提供便捷的物流操作方法。
 */
class LogisticsCoordinator
{
    /**
     * 操作工廠。
     */
    protected OperationFactoryInterface $factory;

    /**
     * 表單產生器。
     */
    protected FormBuilder $formBuilder;

    /**
     * 建立協調器。
     *
     * @param OperationFactoryInterface $factory 操作工廠
     * @param FormBuilder $formBuilder 表單產生器
     */
    public function __construct(OperationFactoryInterface $factory, FormBuilder $formBuilder)
    {
        $this->factory = $factory;
        $this->formBuilder = $formBuilder;
    }

    /**
     * 建立操作物件。
     *
     * @param string $target 目標類別或別名
     * @param array<int, mixed> $parameters 建構參數
     * @return Content
     */
    public function make(string $target, array $parameters = []): Content
    {
        return $this->factory->make($target, $parameters);
    }

    /**
     * 取得操作工廠。
     *
     * @return OperationFactoryInterface
     */
    public function getFactory(): OperationFactoryInterface
    {
        return $this->factory;
    }

    /**
     * 取得表單產生器。
     *
     * @return FormBuilder
     */
    public function getFormBuilder(): FormBuilder
    {
        return $this->formBuilder;
    }

    /**
     * 開啟門市電子地圖。
     *
     * @param string $tradeNo 交易編號
     * @param string|LogisticsSubType $subType 物流子類型
     * @param bool $isCollection 是否代收貨款
     * @param string|null $serverReplyUrl 回覆網址
     * @return array{action: string, fields: array<string, mixed>}
     */
    public function openStoreMap(
        string $tradeNo,
        string|LogisticsSubType $subType,
        bool $isCollection = false,
        ?string $serverReplyUrl = null
    ): array {
        /** @var OpenStoreMap $storeMap */
        $storeMap = $this->factory->make('store_map');
        $storeMap->setMerchantTradeNo($tradeNo);

        if ($subType instanceof LogisticsSubType) {
            $storeMap->setLogisticsSubType($subType);
        } else {
            $storeMap->setLogisticsSubType(LogisticsSubType::from($subType));
        }

        $storeMap->setIsCollection($isCollection ? IsCollection::YES : IsCollection::NO);

        if ($serverReplyUrl !== null) {
            $storeMap->setServerReplyURL($serverReplyUrl);
        }

        return $this->formBuilder->toArray($storeMap);
    }

    /**
     * 建立超商物流訂單。
     *
     * @param array<string, mixed> $data 訂單資料
     * @return Response
     */
    public function createCvsOrder(array $data): Response
    {
        /** @var CreateCvsOrder $order */
        $order = $this->factory->make('cvs.create');

        $this->fillOrderData($order, $data);

        return $order->send();
    }

    /**
     * 建立宅配物流訂單。
     *
     * @param array<string, mixed> $data 訂單資料
     * @return Response
     */
    public function createHomeOrder(array $data): Response
    {
        /** @var CreateHomeOrder $order */
        $order = $this->factory->make('home.create');

        $this->fillOrderData($order, $data);

        return $order->send();
    }

    /**
     * 查詢物流訂單。
     *
     * @param string $logisticsId 綠界物流交易編號
     * @return Response
     */
    public function queryOrder(string $logisticsId): Response
    {
        /** @var QueryLogisticsOrder $query */
        $query = $this->factory->make('queries.order');
        $query->setAllPayLogisticsID($logisticsId);

        return $query->send();
    }

    /**
     * 產生門市地圖自動提交表單。
     *
     * @param string $tradeNo 交易編號
     * @param string|LogisticsSubType $subType 物流子類型
     * @param bool $isCollection 是否代收貨款
     * @param string|null $serverReplyUrl 回覆網址
     * @return string HTML
     */
    public function renderStoreMap(
        string $tradeNo,
        string|LogisticsSubType $subType,
        bool $isCollection = false,
        ?string $serverReplyUrl = null
    ): string {
        /** @var OpenStoreMap $storeMap */
        $storeMap = $this->factory->make('store_map');
        $storeMap->setMerchantTradeNo($tradeNo);

        if ($subType instanceof LogisticsSubType) {
            $storeMap->setLogisticsSubType($subType);
        } else {
            $storeMap->setLogisticsSubType(LogisticsSubType::from($subType));
        }

        $storeMap->setIsCollection($isCollection ? IsCollection::YES : IsCollection::NO);

        if ($serverReplyUrl !== null) {
            $storeMap->setServerReplyURL($serverReplyUrl);
        }

        return $this->formBuilder->autoSubmit($storeMap);
    }

    /**
     * 填入訂單資料。
     *
     * @param Content $order 訂單物件
     * @param array<string, mixed> $data 資料
     */
    protected function fillOrderData(Content $order, array $data): void
    {
        $methodMap = [
            'MerchantTradeNo' => 'setMerchantTradeNo',
            'MerchantTradeDate' => 'setMerchantTradeDate',
            'LogisticsSubType' => 'setLogisticsSubType',
            'GoodsAmount' => 'setGoodsAmount',
            'GoodsName' => 'setGoodsName',
            'SenderName' => 'setSenderName',
            'SenderPhone' => 'setSenderPhone',
            'SenderCellPhone' => 'setSenderCellPhone',
            'SenderZipCode' => 'setSenderZipCode',
            'SenderAddress' => 'setSenderAddress',
            'ReceiverName' => 'setReceiverName',
            'ReceiverPhone' => 'setReceiverPhone',
            'ReceiverCellPhone' => 'setReceiverCellPhone',
            'ReceiverZipCode' => 'setReceiverZipCode',
            'ReceiverAddress' => 'setReceiverAddress',
            'ReceiverEmail' => 'setReceiverEmail',
            'ReceiverStoreID' => 'setReceiverStoreID',
            'ServerReplyURL' => 'setServerReplyURL',
            'IsCollection' => 'setIsCollection',
            'Temperature' => 'setTemperature',
            'Distance' => 'setDistance',
            'Specification' => 'setSpecification',
            'Remark' => 'setRemark',
        ];

        foreach ($data as $key => $value) {
            if (isset($methodMap[$key]) && method_exists($order, $methodMap[$key])) {
                $order->{$methodMap[$key]}($value);
            }
        }
    }
}
