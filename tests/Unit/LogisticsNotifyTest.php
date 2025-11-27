<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Tests\Unit;

use CarlLee\EcPayLogistics\Exceptions\LogisticsException;
use CarlLee\EcPayLogistics\Infrastructure\CheckMacEncoder;
use CarlLee\EcPayLogistics\Notifications\LogisticsNotify;
use CarlLee\EcPayLogistics\Tests\TestCase;

class LogisticsNotifyTest extends TestCase
{
    private LogisticsNotify $notify;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notify = new LogisticsNotify($this->hashKey, $this->hashIV);
    }

    public function test_can_verify_valid_notification(): void
    {
        // 產生有效的通知資料
        $encoder = new CheckMacEncoder($this->hashKey, $this->hashIV);
        $data = [
            'MerchantID' => $this->merchantId,
            'AllPayLogisticsID' => '1234567890',
            'MerchantTradeNo' => 'TEST123',
            'RtnCode' => '300',
            'RtnMsg' => '訂單處理中',
        ];
        $data = $encoder->encodePayload($data);

        $result = $this->notify->verify($data);

        $this->assertTrue($result);
        $this->assertTrue($this->notify->isVerified());
    }

    public function test_verify_fails_with_invalid_check_mac(): void
    {
        $data = [
            'MerchantID' => $this->merchantId,
            'AllPayLogisticsID' => '1234567890',
            'CheckMacValue' => 'INVALID',
        ];

        $result = $this->notify->verify($data);

        $this->assertFalse($result);
        $this->assertFalse($this->notify->isVerified());
    }

    public function test_verify_or_fail_throws_exception(): void
    {
        $this->expectException(LogisticsException::class);

        $data = [
            'MerchantID' => $this->merchantId,
            'CheckMacValue' => 'INVALID',
        ];

        $this->notify->verifyOrFail($data);
    }

    public function test_can_get_data(): void
    {
        $encoder = new CheckMacEncoder($this->hashKey, $this->hashIV);
        $data = [
            'MerchantID' => $this->merchantId,
            'AllPayLogisticsID' => '1234567890',
            'MerchantTradeNo' => 'TEST123',
        ];
        $data = $encoder->encodePayload($data);

        $this->notify->verify($data);

        $retrievedData = $this->notify->getData();

        $this->assertEquals($this->merchantId, $retrievedData['MerchantID']);
        $this->assertEquals('1234567890', $retrievedData['AllPayLogisticsID']);
    }

    public function test_can_get_all_pay_logistics_id(): void
    {
        $encoder = new CheckMacEncoder($this->hashKey, $this->hashIV);
        $data = [
            'AllPayLogisticsID' => '9876543210',
        ];
        $data = $encoder->encodePayload($data);

        $this->notify->verify($data);

        $this->assertEquals('9876543210', $this->notify->getAllPayLogisticsID());
    }

    public function test_can_get_merchant_trade_no(): void
    {
        $encoder = new CheckMacEncoder($this->hashKey, $this->hashIV);
        $data = [
            'MerchantTradeNo' => 'ORDER123',
        ];
        $data = $encoder->encodePayload($data);

        $this->notify->verify($data);

        $this->assertEquals('ORDER123', $this->notify->getMerchantTradeNo());
    }

    public function test_can_get_rtn_code(): void
    {
        $encoder = new CheckMacEncoder($this->hashKey, $this->hashIV);
        $data = [
            'RtnCode' => '300',
        ];
        $data = $encoder->encodePayload($data);

        $this->notify->verify($data);

        $this->assertEquals('300', $this->notify->getRtnCode());
    }

    public function test_can_get_rtn_msg(): void
    {
        $encoder = new CheckMacEncoder($this->hashKey, $this->hashIV);
        $data = [
            'RtnMsg' => '訂單處理中',
        ];
        $data = $encoder->encodePayload($data);

        $this->notify->verify($data);

        $this->assertEquals('訂單處理中', $this->notify->getRtnMsg());
    }

    public function test_is_success_for_successful_codes(): void
    {
        $encoder = new CheckMacEncoder($this->hashKey, $this->hashIV);

        $successCodes = ['300', '2030', '2063', '2067', '2073', '3018'];

        foreach ($successCodes as $code) {
            $data = $encoder->encodePayload(['RtnCode' => $code]);
            $this->notify->verify($data);
            $this->assertTrue($this->notify->isSuccess(), "Code {$code} should be success");
        }
    }

    public function test_is_not_success_for_other_codes(): void
    {
        $encoder = new CheckMacEncoder($this->hashKey, $this->hashIV);
        $data = $encoder->encodePayload(['RtnCode' => '999']);

        $this->notify->verify($data);

        $this->assertFalse($this->notify->isSuccess());
    }

    public function test_get_success_response(): void
    {
        $this->assertEquals('1|OK', $this->notify->getSuccessResponse());
    }

    public function test_can_get_custom_field(): void
    {
        $encoder = new CheckMacEncoder($this->hashKey, $this->hashIV);
        $data = $encoder->encodePayload([
            'GoodsAmount' => '1000',
        ]);

        $this->notify->verify($data);

        $this->assertEquals(1000, $this->notify->getGoodsAmount());
    }

    public function test_get_returns_default_for_missing_field(): void
    {
        $encoder = new CheckMacEncoder($this->hashKey, $this->hashIV);
        $data = $encoder->encodePayload([]);

        $this->notify->verify($data);

        $this->assertEquals('default', $this->notify->get('NonExistentField', 'default'));
    }
}
