<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics\Tests\Unit;

use CarlLee\EcPayLogistics\Parameter\Distance;
use CarlLee\EcPayLogistics\Parameter\IsCollection;
use CarlLee\EcPayLogistics\Parameter\LogisticsSubType;
use CarlLee\EcPayLogistics\Parameter\LogisticsType;
use CarlLee\EcPayLogistics\Parameter\Specification;
use CarlLee\EcPayLogistics\Parameter\Temperature;
use CarlLee\EcPayLogistics\Tests\TestCase;

class ParameterEnumTest extends TestCase
{
    public function test_logistics_type_values(): void
    {
        $this->assertEquals('CVS', LogisticsType::CVS->value);
        $this->assertEquals('Home', LogisticsType::HOME->value);
    }

    public function test_logistics_type_labels(): void
    {
        $this->assertEquals('超商取貨', LogisticsType::CVS->label());
        $this->assertEquals('宅配', LogisticsType::HOME->label());
    }

    public function test_logistics_sub_type_cvs_values(): void
    {
        $this->assertEquals('UNIMART', LogisticsSubType::UNIMART->value);
        $this->assertEquals('UNIMARTC2C', LogisticsSubType::UNIMART_C2C->value);
        $this->assertEquals('FAMI', LogisticsSubType::FAMI->value);
        $this->assertEquals('FAMIC2C', LogisticsSubType::FAMI_C2C->value);
        $this->assertEquals('HILIFE', LogisticsSubType::HILIFE->value);
        $this->assertEquals('HILIFEC2C', LogisticsSubType::HILIFE_C2C->value);
        $this->assertEquals('OKMARTC2C', LogisticsSubType::OKMART_C2C->value);
    }

    public function test_logistics_sub_type_home_values(): void
    {
        $this->assertEquals('TCAT', LogisticsSubType::TCAT->value);
        $this->assertEquals('POST', LogisticsSubType::POST->value);
    }

    public function test_logistics_sub_type_is_c2c(): void
    {
        $this->assertTrue(LogisticsSubType::UNIMART_C2C->isC2C());
        $this->assertTrue(LogisticsSubType::FAMI_C2C->isC2C());
        $this->assertTrue(LogisticsSubType::HILIFE_C2C->isC2C());
        $this->assertTrue(LogisticsSubType::OKMART_C2C->isC2C());

        $this->assertFalse(LogisticsSubType::UNIMART->isC2C());
        $this->assertFalse(LogisticsSubType::FAMI->isC2C());
        $this->assertFalse(LogisticsSubType::TCAT->isC2C());
    }

    public function test_logistics_sub_type_is_b2c(): void
    {
        $this->assertTrue(LogisticsSubType::UNIMART->isB2C());
        $this->assertTrue(LogisticsSubType::FAMI->isB2C());
        $this->assertTrue(LogisticsSubType::HILIFE->isB2C());

        $this->assertFalse(LogisticsSubType::UNIMART_C2C->isB2C());
        $this->assertFalse(LogisticsSubType::TCAT->isB2C());
    }

    public function test_logistics_sub_type_is_cvs(): void
    {
        $this->assertTrue(LogisticsSubType::UNIMART->isCvs());
        $this->assertTrue(LogisticsSubType::UNIMART_C2C->isCvs());
        $this->assertTrue(LogisticsSubType::FAMI->isCvs());
        $this->assertTrue(LogisticsSubType::FAMI_C2C->isCvs());

        $this->assertFalse(LogisticsSubType::TCAT->isCvs());
        $this->assertFalse(LogisticsSubType::POST->isCvs());
    }

    public function test_logistics_sub_type_is_home(): void
    {
        $this->assertTrue(LogisticsSubType::TCAT->isHome());
        $this->assertTrue(LogisticsSubType::POST->isHome());

        $this->assertFalse(LogisticsSubType::UNIMART->isHome());
        $this->assertFalse(LogisticsSubType::FAMI_C2C->isHome());
    }

    public function test_logistics_sub_type_get_logistics_type(): void
    {
        $this->assertEquals(LogisticsType::CVS, LogisticsSubType::UNIMART->getLogisticsType());
        $this->assertEquals(LogisticsType::CVS, LogisticsSubType::FAMI_C2C->getLogisticsType());
        $this->assertEquals(LogisticsType::HOME, LogisticsSubType::TCAT->getLogisticsType());
        $this->assertEquals(LogisticsType::HOME, LogisticsSubType::POST->getLogisticsType());
    }

    public function test_is_collection_values(): void
    {
        $this->assertEquals('N', IsCollection::NO->value);
        $this->assertEquals('Y', IsCollection::YES->value);
    }

    public function test_is_collection_helper(): void
    {
        $this->assertFalse(IsCollection::NO->isCollection());
        $this->assertTrue(IsCollection::YES->isCollection());
    }

    public function test_temperature_values(): void
    {
        $this->assertEquals('0001', Temperature::ROOM->value);
        $this->assertEquals('0002', Temperature::REFRIGERATION->value);
        $this->assertEquals('0003', Temperature::FREEZE->value);
    }

    public function test_distance_values(): void
    {
        $this->assertEquals('00', Distance::SAME->value);
        $this->assertEquals('01', Distance::OTHER->value);
        $this->assertEquals('02', Distance::ISLAND->value);
    }

    public function test_specification_values(): void
    {
        $this->assertEquals('0001', Specification::SIZE_60->value);
        $this->assertEquals('0002', Specification::SIZE_90->value);
        $this->assertEquals('0003', Specification::SIZE_120->value);
        $this->assertEquals('0004', Specification::SIZE_150->value);
    }

    public function test_specification_max_size(): void
    {
        $this->assertEquals(60, Specification::SIZE_60->maxSize());
        $this->assertEquals(90, Specification::SIZE_90->maxSize());
        $this->assertEquals(120, Specification::SIZE_120->maxSize());
        $this->assertEquals(150, Specification::SIZE_150->maxSize());
    }
}
