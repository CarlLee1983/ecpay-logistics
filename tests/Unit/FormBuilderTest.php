<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Tests\Unit;

use CarlLee\EcPayLogistics\FormBuilder;
use CarlLee\EcPayLogistics\Operations\StoreMap\OpenStoreMap;
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;
use CarlLee\EcPayLogistics\Tests\TestCase;

class FormBuilderTest extends TestCase
{
    private FormBuilder $formBuilder;
    private OpenStoreMap $storeMap;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formBuilder = new FormBuilder($this->serverUrl);
        $this->storeMap = new OpenStoreMap(
            $this->merchantId,
            $this->hashKey,
            $this->hashIV
        );

        $this->storeMap
            ->setMerchantTradeNo('TEST123')
            ->setLogisticsSubType(LogisticsSubType::UNIMART_C2C)
            ->setServerReplyURL('https://example.com/callback');
    }

    public function test_can_build_form(): void
    {
        $html = $this->formBuilder->build($this->storeMap);

        $this->assertStringContainsString('<form', $html);
        $this->assertStringContainsString('method="post"', $html);
        $this->assertStringContainsString('action="', $html);
        $this->assertStringContainsString('</form>', $html);
        $this->assertStringContainsString('MerchantID', $html);
    }

    public function test_can_build_auto_submit_form(): void
    {
        $html = $this->formBuilder->autoSubmit($this->storeMap);

        $this->assertStringContainsString('<!DOCTYPE html>', $html);
        $this->assertStringContainsString('<form', $html);
        $this->assertStringContainsString('submit()', $html);
        $this->assertStringContainsString('正在導向綠界物流頁面', $html);
    }

    public function test_can_get_action_url(): void
    {
        $url = $this->formBuilder->getActionUrl($this->storeMap);

        $this->assertEquals(
            $this->serverUrl . '/Express/map',
            $url
        );
    }

    public function test_can_get_fields(): void
    {
        $fields = $this->formBuilder->getFields($this->storeMap);

        $this->assertIsArray($fields);
        $this->assertArrayHasKey('MerchantID', $fields);
        $this->assertArrayHasKey('MerchantTradeNo', $fields);
        $this->assertArrayHasKey('LogisticsType', $fields);
        $this->assertArrayHasKey('LogisticsSubType', $fields);
        $this->assertArrayHasKey('CheckMacValue', $fields);
    }

    public function test_can_convert_to_json(): void
    {
        $json = $this->formBuilder->toJson($this->storeMap);

        $decoded = json_decode($json, true);

        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('action', $decoded);
        $this->assertArrayHasKey('fields', $decoded);
        $this->assertEquals(
            $this->serverUrl . '/Express/map',
            $decoded['action']
        );
    }

    public function test_can_convert_to_array(): void
    {
        $array = $this->formBuilder->toArray($this->storeMap);

        $this->assertIsArray($array);
        $this->assertArrayHasKey('action', $array);
        $this->assertArrayHasKey('fields', $array);
    }

    public function test_can_set_server_url(): void
    {
        $this->formBuilder->setServerUrl('https://new-server.com');

        $this->assertEquals('https://new-server.com', $this->formBuilder->getServerUrl());
    }

    public function test_server_url_trailing_slash_is_trimmed(): void
    {
        $this->formBuilder->setServerUrl('https://example.com/');

        $this->assertEquals('https://example.com', $this->formBuilder->getServerUrl());
    }

    public function test_form_escapes_html_special_chars(): void
    {
        $html = $this->formBuilder->build($this->storeMap);

        // 確保不包含未轉義的特殊字元
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_custom_form_id(): void
    {
        $html = $this->formBuilder->build($this->storeMap, 'custom-form-id');

        $this->assertStringContainsString('id="custom-form-id"', $html);
    }

    public function test_custom_submit_text(): void
    {
        $html = $this->formBuilder->build($this->storeMap, 'form', '送出');

        $this->assertStringContainsString('送出', $html);
    }

    public function test_custom_loading_text(): void
    {
        $html = $this->formBuilder->autoSubmit($this->storeMap, 'form', '載入中...');

        $this->assertStringContainsString('載入中...', $html);
    }
}
