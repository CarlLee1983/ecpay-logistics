<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Tests\Unit;

use CarlLee\EcPayLogistics\Exceptions\LogisticsException;
use CarlLee\EcPayLogistics\Operations\Cvs\CreateCvsOrder;
use CarlLee\EcPayLogistics\Parameter\IsCollection;
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;
use CarlLee\EcPayLogistics\Parameter\LogisticsType;
use CarlLee\EcPayLogistics\Tests\TestCase;

class CreateCvsOrderTest extends TestCase
{
    private CreateCvsOrder $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->order = new CreateCvsOrder(
            $this->merchantId,
            $this->hashKey,
            $this->hashIV
        );
    }

    /**
     * 填入完整的訂單資料以通過驗證。
     */
    private function fillCompleteOrder(): void
    {
        $this->order
            ->setMerchantTradeNo('TEST123')
            ->setGoodsName('測試商品')
            ->setGoodsAmount(100)
            ->setSenderName('測試寄件人')
            ->setSenderCellPhone('0912345678')
            ->setReceiverName('測試收件人')
            ->setReceiverCellPhone('0987654321')
            ->setReceiverStoreID('991182')
            ->setServerReplyURL('https://example.com/callback');
    }

    public function test_can_set_merchant_trade_no(): void
    {
        $this->fillCompleteOrder();
        $this->order->setMerchantTradeNo('CUSTOM123');

        $payload = $this->order->getPayload();

        $this->assertEquals('CUSTOM123', $payload['MerchantTradeNo']);
    }

    public function test_throws_exception_for_too_long_merchant_trade_no(): void
    {
        $this->expectException(LogisticsException::class);

        $this->order->setMerchantTradeNo(str_repeat('A', 21));
    }

    public function test_can_set_logistics_sub_type(): void
    {
        $this->fillCompleteOrder();
        $this->order->setLogisticsSubType(LogisticsSubType::FAMI_C2C);

        $payload = $this->order->getPayload();

        $this->assertEquals(LogisticsSubType::FAMI_C2C->value, $payload['LogisticsSubType']);
        $this->assertEquals(LogisticsType::CVS->value, $payload['LogisticsType']);
    }

    public function test_throws_exception_for_home_sub_type(): void
    {
        $this->expectException(LogisticsException::class);

        $this->order->setLogisticsSubType(LogisticsSubType::TCAT);
    }

    public function test_can_set_goods_amount(): void
    {
        $this->fillCompleteOrder();
        $this->order->setGoodsAmount(1000);

        $payload = $this->order->getPayload();

        $this->assertEquals(1000, $payload['GoodsAmount']);
    }

    public function test_throws_exception_for_negative_goods_amount(): void
    {
        $this->expectException(LogisticsException::class);

        $this->order->setGoodsAmount(-100);
    }

    public function test_can_set_is_collection(): void
    {
        $this->fillCompleteOrder();
        $this->order->setIsCollection(IsCollection::YES);

        $payload = $this->order->getPayload();

        $this->assertEquals(IsCollection::YES->value, $payload['IsCollection']);
    }

    public function test_can_set_sender_info(): void
    {
        $this->fillCompleteOrder();
        $this->order
            ->setSenderName('新寄件人')
            ->setSenderCellPhone('0911111111')
            ->setSenderPhone('02-12345678');

        $payload = $this->order->getPayload();

        $this->assertEquals('新寄件人', $payload['SenderName']);
        $this->assertEquals('0911111111', $payload['SenderCellPhone']);
        $this->assertEquals('02-12345678', $payload['SenderPhone']);
    }

    public function test_can_set_receiver_info(): void
    {
        $this->fillCompleteOrder();
        $this->order
            ->setReceiverName('新收件人')
            ->setReceiverCellPhone('0922222222')
            ->setReceiverEmail('test@example.com')
            ->setReceiverStoreID('123456');

        $payload = $this->order->getPayload();

        $this->assertEquals('新收件人', $payload['ReceiverName']);
        $this->assertEquals('0922222222', $payload['ReceiverCellPhone']);
        $this->assertEquals('test@example.com', $payload['ReceiverEmail']);
        $this->assertEquals('123456', $payload['ReceiverStoreID']);
    }

    public function test_validation_fails_without_merchant_trade_no(): void
    {
        $this->expectException(LogisticsException::class);
        $this->expectExceptionMessage('MerchantTradeNo');

        $this->order
            ->setGoodsName('測試商品')
            ->setSenderName('測試寄件人')
            ->setSenderCellPhone('0912345678')
            ->setReceiverName('測試收件人')
            ->setReceiverCellPhone('0987654321')
            ->setReceiverStoreID('991182')
            ->setServerReplyURL('https://example.com/callback')
            ->getPayload();
    }

    public function test_validation_fails_without_sender_phone(): void
    {
        $this->expectException(LogisticsException::class);

        $this->order
            ->setMerchantTradeNo('TEST123')
            ->setGoodsName('測試商品')
            ->setSenderName('測試寄件人')
            ->setReceiverName('測試收件人')
            ->setReceiverCellPhone('0987654321')
            ->setReceiverStoreID('991182')
            ->setServerReplyURL('https://example.com/callback')
            ->getPayload();
    }

    public function test_convenience_methods(): void
    {
        $this->fillCompleteOrder();

        $this->order->useUnimartC2C();
        $payload = $this->order->getPayload();
        $this->assertEquals(LogisticsSubType::UNIMART_C2C->value, $payload['LogisticsSubType']);

        $this->order->useFamiC2C();
        $payload = $this->order->getPayload();
        $this->assertEquals(LogisticsSubType::FAMI_C2C->value, $payload['LogisticsSubType']);

        $this->order->withCollection();
        $payload = $this->order->getPayload();
        $this->assertEquals(IsCollection::YES->value, $payload['IsCollection']);

        $this->order->withoutCollection();
        $payload = $this->order->getPayload();
        $this->assertEquals(IsCollection::NO->value, $payload['IsCollection']);
    }

    public function test_get_content_includes_check_mac_value(): void
    {
        $this->fillCompleteOrder();

        $content = $this->order->getContent();

        $this->assertArrayHasKey('CheckMacValue', $content);
    }

    public function test_request_path_is_correct(): void
    {
        $this->assertEquals('/Express/Create', $this->order->getRequestPath());
    }
}
