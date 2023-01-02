<?php

namespace tests\Feature\BO;

use PHPUnit\Framework\TestCase;
use src\BO\ShippingCorreiosBO;
use src\DTO\ShippingPackageDTO;
use src\Enums\ShippingCorreiosEnum;
use src\Exceptions\ShippingExceptions\InvalidParamCalculateException;
use src\Exceptions\ShippingExceptions\UnableToCalculateShipping;

class ShippingCorreiosBoFeatureTest extends TestCase
{
    public ShippingCorreiosBO $correios;
    public ShippingPackageDTO $package;

    protected function setUp(): void
    {
        $this->correios = new ShippingCorreiosBO('88780-000');
        $this->package = new ShippingPackageDTO();
    }

    public function testMaxSumDimensionPackage()
    {
        $this->package->setWidth(10);
        $this->package->setHeight(100);
        $this->package->setLength(100);
        $this->package->setGrossWeight(1);
        $this->expectException(InvalidParamCalculateException::class);
        $this->expectExceptionMessage('A soma das dimensões não pode ultrapassar 200 centímetros!');
        $this->correios->calculateShipping($this->package, '88790-000');
    }

    public function testInvalidPackageGrossWeight()
    {
        $this->package->setWidth(1);
        $this->package->setHeight(1);
        $this->package->setLength(1);
        $this->package->setGrossWeight(35);
        $this->expectException(InvalidParamCalculateException::class);
        $this->expectExceptionMessage('O peso máximo aceito é de 30 KG!');
        $this->correios->calculateShipping($this->package, '88790-000');
    }

    /**
     * @return void
     * @dataProvider dataProviderInvalidDimensionPackage
     */
    public function testInvalidDimensionPackage(int $width, int $height, int $length)
    {
        $this->package->setWidth($width);
        $this->package->setHeight($height);
        $this->package->setLength($length);
        $this->package->setGrossWeight(1);
        $this->expectException(InvalidParamCalculateException::class);
        $this->expectExceptionMessage('Dimensão maior que o permitido, nenhuma dimensão deve ultrapassar 100 centímetros!');
        $this->correios->calculateShipping($this->package, '88790-000');
    }

    public function dataProviderInvalidDimensionPackage(): array
    {
        return array(
            'maxWidthExceeded' => array('width' => 101, 'height' => 10, 'length' => 15),
            'maxHeightExceeded' => array('width' => 10, 'height' => 101, 'length' => 15),
            'maxLengthExceeded' => array('width' => 15, 'height' => 10, 'length' => 101)
        );
    }

    /**
     * @return void
     * @dataProvider dataProviderInvalidZipCodeSize
     */
    public function testInvalidZipCodeSize($zipCode)
    {
        $this->package->setWidth(10);
        $this->package->setHeight(15);
        $this->package->setLength(20);
        $this->package->setGrossWeight(10);
        $this->expectException(InvalidParamCalculateException::class);
        $this->expectExceptionMessage('CEP de destino inválido!');
        $this->correios->calculateShipping($this->package, $zipCode);
    }

    public function dataProviderInvalidZipCodeSize(): array
    {
        return array(
            'littleZipCode' => array('zipCode' => '1234'),
            'smallZipCode' => array('zipCode' => '123456789')
        );
    }

    public function testInvalidZipCode()
    {
        $this->package->setWidth(10);
        $this->package->setHeight(15);
        $this->package->setLength(20);
        $this->package->setGrossWeight(10);
        $this->expectException(UnableToCalculateShipping::class);
        $this->correios->calculateShipping($this->package, '00000000');
        $this->assertTrue(true);
    }

    public function testValidCalculate()
    {
        $this->package->setWidth(10);
        $this->package->setHeight(15);
        $this->package->setLength(20);
        $this->package->setGrossWeight(0.100);
        $calc = $this->correios->calculateShipping($this->package, '88790-000');
        $this->assertIsArray($calc);
        $this->assertCount(2, $calc);
        $this->assertArrayHasKey(ShippingCorreiosEnum::PAC, $calc);
        $this->assertArrayHasKey(ShippingCorreiosEnum::SEDEX, $calc);
    }
}