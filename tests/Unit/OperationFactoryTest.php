<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Tests\Unit;

use CarlLee\EcPayLogistics\Content;
use CarlLee\EcPayLogistics\Factories\OperationFactory;
use CarlLee\EcPayLogistics\Operations\Cvs\CreateCvsOrder;
use CarlLee\EcPayLogistics\Operations\Home\CreateHomeOrder;
use CarlLee\EcPayLogistics\Operations\StoreMap\OpenStoreMap;
use CarlLee\EcPayLogistics\Printing\PrintCvsDocument;
use CarlLee\EcPayLogistics\Printing\PrintTradeDocument;
use CarlLee\EcPayLogistics\Queries\GetStoreList;
use CarlLee\EcPayLogistics\Queries\QueryLogisticsOrder;
use CarlLee\EcPayLogistics\Tests\TestCase;
use InvalidArgumentException;

class OperationFactoryTest extends TestCase
{
    private OperationFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new OperationFactory([
            'merchant_id' => $this->merchantId,
            'hash_key' => $this->hashKey,
            'hash_iv' => $this->hashIV,
            'server_url' => $this->serverUrl,
        ]);
    }

    public function test_can_create_store_map(): void
    {
        $storeMap = $this->factory->make('store_map');

        $this->assertInstanceOf(OpenStoreMap::class, $storeMap);
        $this->assertInstanceOf(Content::class, $storeMap);
    }

    public function test_can_create_cvs_order(): void
    {
        $order = $this->factory->make('cvs.create');

        $this->assertInstanceOf(CreateCvsOrder::class, $order);
    }

    public function test_can_create_home_order(): void
    {
        $order = $this->factory->make('home.create');

        $this->assertInstanceOf(CreateHomeOrder::class, $order);
    }

    public function test_can_create_query_order(): void
    {
        $query = $this->factory->make('queries.order');

        $this->assertInstanceOf(QueryLogisticsOrder::class, $query);
    }

    public function test_can_create_store_list(): void
    {
        $query = $this->factory->make('queries.store_list');

        $this->assertInstanceOf(GetStoreList::class, $query);
    }

    public function test_can_create_print_trade(): void
    {
        $print = $this->factory->make('printing.trade');

        $this->assertInstanceOf(PrintTradeDocument::class, $print);
    }

    public function test_can_create_print_cvs(): void
    {
        $print = $this->factory->make('printing.cvs');

        $this->assertInstanceOf(PrintCvsDocument::class, $print);
    }

    public function test_can_register_alias(): void
    {
        $this->factory->alias('my_alias', CreateCvsOrder::class);

        $order = $this->factory->make('my_alias');

        $this->assertInstanceOf(CreateCvsOrder::class, $order);
    }

    public function test_can_extend_with_resolver(): void
    {
        $this->factory->extend('custom', function ($params, $factory) {
            $order = new CreateCvsOrder(
                $factory->getCredentials()['merchant_id'],
                $factory->getCredentials()['hash_key'],
                $factory->getCredentials()['hash_iv']
            );
            $order->setGoodsAmount(999);

            return $order;
        });

        $order = $this->factory->make('custom');

        $this->assertInstanceOf(CreateCvsOrder::class, $order);
    }

    public function test_can_add_initializer(): void
    {
        $remarkValue = 'initialized_' . uniqid();

        $this->factory->addInitializer(function (Content $content) use ($remarkValue) {
            $content->setRemark($remarkValue);
        });

        /** @var CreateCvsOrder $order */
        $order = $this->factory->make('cvs.create');

        // 填入完整資料以通過驗證
        $order
            ->setMerchantTradeNo('TEST123')
            ->setGoodsName('測試商品')
            ->setGoodsAmount(100)
            ->setSenderName('測試寄件人')
            ->setSenderCellPhone('0912345678')
            ->setReceiverName('測試收件人')
            ->setReceiverCellPhone('0987654321')
            ->setReceiverStoreID('991182')
            ->setServerReplyURL('https://example.com/callback');

        $payload = $order->getPayload();

        // 確認 Remark 被設定了
        $this->assertArrayHasKey('Remark', $payload);
        $this->assertEquals($remarkValue, $payload['Remark']);
    }

    public function test_throws_exception_for_invalid_target(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->factory->make('invalid.operation');
    }

    public function test_credentials_are_set_correctly(): void
    {
        $credentials = $this->factory->getCredentials();

        $this->assertEquals($this->merchantId, $credentials['merchant_id']);
        $this->assertEquals($this->hashKey, $credentials['hash_key']);
        $this->assertEquals($this->hashIV, $credentials['hash_iv']);
    }

    public function test_server_url_is_set_correctly(): void
    {
        $this->assertEquals($this->serverUrl, $this->factory->getServerUrl());
    }

    public function test_can_set_credentials(): void
    {
        $this->factory->setCredentials('new_id', 'new_key', 'new_iv');

        $credentials = $this->factory->getCredentials();

        $this->assertEquals('new_id', $credentials['merchant_id']);
        $this->assertEquals('new_key', $credentials['hash_key']);
        $this->assertEquals('new_iv', $credentials['hash_iv']);
    }

    public function test_can_use_full_class_name(): void
    {
        $order = $this->factory->make(CreateCvsOrder::class);

        $this->assertInstanceOf(CreateCvsOrder::class, $order);
    }
}
