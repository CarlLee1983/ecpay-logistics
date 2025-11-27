<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Tests\Unit;

use CarlLee\EcPayLogistics\Exceptions\LogisticsException;
use CarlLee\EcPayLogistics\Infrastructure\CheckMacEncoder;
use CarlLee\EcPayLogistics\Tests\TestCase;

class CheckMacEncoderTest extends TestCase
{
    private CheckMacEncoder $encoder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->encoder = new CheckMacEncoder($this->hashKey, $this->hashIV);
    }

    public function test_can_generate_check_mac_value(): void
    {
        $payload = [
            'MerchantID' => '2000132',
            'MerchantTradeNo' => 'TEST123',
            'LogisticsType' => 'CVS',
        ];

        $checkMac = $this->encoder->generateCheckMacValue($payload);

        $this->assertNotEmpty($checkMac);
        $this->assertEquals(32, strlen($checkMac)); // MD5 產生 32 字元
        $this->assertEquals(strtoupper($checkMac), $checkMac); // 應為大寫
    }

    public function test_can_encode_payload(): void
    {
        $payload = [
            'MerchantID' => '2000132',
            'MerchantTradeNo' => 'TEST123',
        ];

        $encoded = $this->encoder->encodePayload($payload);

        $this->assertArrayHasKey('CheckMacValue', $encoded);
        $this->assertArrayHasKey('MerchantID', $encoded);
        $this->assertArrayHasKey('MerchantTradeNo', $encoded);
    }

    public function test_can_verify_response(): void
    {
        $payload = [
            'MerchantID' => '2000132',
            'MerchantTradeNo' => 'TEST123',
        ];

        $encoded = $this->encoder->encodePayload($payload);

        $this->assertTrue($this->encoder->verifyResponse($encoded));
    }

    public function test_verify_fails_with_wrong_check_mac(): void
    {
        $payload = [
            'MerchantID' => '2000132',
            'MerchantTradeNo' => 'TEST123',
            'CheckMacValue' => 'WRONGVALUE',
        ];

        $this->assertFalse($this->encoder->verifyResponse($payload));
    }

    public function test_verify_fails_without_check_mac(): void
    {
        $payload = [
            'MerchantID' => '2000132',
            'MerchantTradeNo' => 'TEST123',
        ];

        $this->assertFalse($this->encoder->verifyResponse($payload));
    }

    public function test_verify_or_fail_throws_exception(): void
    {
        $this->expectException(LogisticsException::class);

        $payload = [
            'MerchantID' => '2000132',
            'CheckMacValue' => 'WRONGVALUE',
        ];

        $this->encoder->verifyOrFail($payload);
    }

    public function test_check_mac_is_consistent(): void
    {
        $payload = [
            'MerchantID' => '2000132',
            'MerchantTradeNo' => 'TEST123',
            'LogisticsType' => 'CVS',
        ];

        $checkMac1 = $this->encoder->generateCheckMacValue($payload);
        $checkMac2 = $this->encoder->generateCheckMacValue($payload);

        $this->assertEquals($checkMac1, $checkMac2);
    }

    public function test_check_mac_changes_with_different_data(): void
    {
        $payload1 = [
            'MerchantID' => '2000132',
            'MerchantTradeNo' => 'TEST123',
        ];

        $payload2 = [
            'MerchantID' => '2000132',
            'MerchantTradeNo' => 'TEST456',
        ];

        $checkMac1 = $this->encoder->generateCheckMacValue($payload1);
        $checkMac2 = $this->encoder->generateCheckMacValue($payload2);

        $this->assertNotEquals($checkMac1, $checkMac2);
    }
}
