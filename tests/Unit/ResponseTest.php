<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Tests\Unit;

use CarlLee\EcPayLogistics\Exceptions\LogisticsException;
use CarlLee\EcPayLogistics\Infrastructure\CheckMacEncoder;
use CarlLee\EcPayLogistics\Response;
use CarlLee\EcPayLogistics\Tests\TestCase;

class ResponseTest extends TestCase
{
    public function test_can_parse_url_encoded_body(): void
    {
        $body = 'RtnCode=1&RtnMsg=OK&AllPayLogisticsID=123456';
        $response = new Response($body);

        $this->assertEquals('1', $response->getRtnCode());
        $this->assertEquals('OK', $response->getRtnMsg());
        $this->assertEquals('123456', $response->getAllPayLogisticsID());
        $this->assertFalse($response->hasParseError());
    }

    public function test_can_parse_json_body(): void
    {
        $body = json_encode([
            'RtnCode' => '1',
            'RtnMsg' => 'Success',
            'AllPayLogisticsID' => '789012',
        ]);

        $response = new Response($body);

        $this->assertEquals('1', $response->getRtnCode());
        $this->assertEquals('Success', $response->getRtnMsg());
        $this->assertEquals('789012', $response->getAllPayLogisticsID());
        $this->assertFalse($response->hasParseError());
    }

    public function test_handles_empty_body(): void
    {
        $response = new Response('');

        $this->assertTrue($response->hasParseError());
        $this->assertEquals('回應內容為空', $response->getParseError());
        $this->assertEmpty($response->getData());
    }

    public function test_is_success_for_code_1(): void
    {
        $body = 'RtnCode=1&RtnMsg=OK';
        $response = new Response($body);

        $this->assertTrue($response->isSuccess());
    }

    public function test_is_success_for_code_300(): void
    {
        $body = 'RtnCode=300&RtnMsg=OK';
        $response = new Response($body);

        $this->assertTrue($response->isSuccess());
    }

    public function test_is_not_success_for_error_code(): void
    {
        $body = 'RtnCode=999&RtnMsg=Error';
        $response = new Response($body);

        $this->assertFalse($response->isSuccess());
    }

    public function test_can_verify_with_encoder(): void
    {
        $encoder = new CheckMacEncoder($this->hashKey, $this->hashIV);

        $data = [
            'RtnCode' => '1',
            'RtnMsg' => 'OK',
        ];
        $encoded = $encoder->encodePayload($data);

        $body = http_build_query($encoded);
        $response = new Response($body, $encoder);

        $this->assertTrue($response->verify());
        $this->assertTrue($response->isVerified());
    }

    public function test_verify_fails_without_encoder(): void
    {
        $body = 'RtnCode=1&RtnMsg=OK';
        $response = new Response($body);

        $this->assertFalse($response->verify());
    }

    public function test_verify_or_fail_throws_exception(): void
    {
        $this->expectException(LogisticsException::class);

        $encoder = new CheckMacEncoder($this->hashKey, $this->hashIV);
        $body = 'RtnCode=1&RtnMsg=OK&CheckMacValue=INVALID';

        $response = new Response($body, $encoder);
        $response->verifyOrFail();
    }

    public function test_can_get_cvs_fields(): void
    {
        $body = 'CVSStoreID=123456&CVSStoreName=TestStore&CVSAddress=Test+Address&CVSTelephone=0912345678&CVSPaymentNo=PAY123&CVSValidationNo=VAL456';

        $response = new Response($body);

        $this->assertEquals('123456', $response->getCVSStoreID());
        $this->assertEquals('TestStore', $response->getCVSStoreName());
        $this->assertEquals('Test Address', $response->getCVSAddress());
        $this->assertEquals('0912345678', $response->getCVSTelephone());
        $this->assertEquals('PAY123', $response->getCVSPaymentNo());
        $this->assertEquals('VAL456', $response->getCVSValidationNo());
    }

    public function test_can_get_booking_note(): void
    {
        $body = 'BookingNote=BN123456';
        $response = new Response($body);

        $this->assertEquals('BN123456', $response->getBookingNote());
    }

    public function test_can_get_print_url(): void
    {
        $body = 'PrintURL=https://example.com/print';
        $response = new Response($body);

        $this->assertEquals('https://example.com/print', $response->getPrintUrl());
    }

    public function test_can_get_raw_body(): void
    {
        $body = 'RtnCode=1&RtnMsg=OK';
        $response = new Response($body);

        $this->assertEquals($body, $response->getRawBody());
    }

    public function test_can_convert_to_array(): void
    {
        $body = 'RtnCode=1&RtnMsg=OK';
        $response = new Response($body);

        $array = $response->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('1', $array['RtnCode']);
        $this->assertEquals('OK', $array['RtnMsg']);
    }

    public function test_get_with_default(): void
    {
        $body = 'RtnCode=1';
        $response = new Response($body);

        $this->assertEquals('default', $response->get('NonExistent', 'default'));
        $this->assertNull($response->get('NonExistent'));
    }

    public function test_handles_json_with_empty_key(): void
    {
        // 純 JSON 格式會被正確解析
        $body = '{"RtnCode":"1","RtnMsg":"OK"}';
        $response = new Response($body);

        $this->assertFalse($response->hasParseError());
        $this->assertEquals('1', $response->getRtnCode());
    }

    public function test_invalid_url_parse_detected(): void
    {
        // 測試 JSON 格式被誤解析為 URL 編碼時的處理
        $body = '{"key":"value"}';
        $response = new Response($body);

        // JSON 應該被正確識別和解析
        $this->assertFalse($response->hasParseError());
        $this->assertEquals('value', $response->get('key'));
    }

    public function test_prefers_url_encoded_over_json(): void
    {
        // 有效的 URL 編碼格式應該優先
        $body = 'key1=value1&key2=value2';
        $response = new Response($body);

        $this->assertFalse($response->hasParseError());
        $this->assertEquals('value1', $response->get('key1'));
        $this->assertEquals('value2', $response->get('key2'));
    }
}

