<?php

namespace src\BO;

use src\DTO\ShippingPackageDTO;
use src\Enums\ShippingCorreiosEnum;
use src\Exceptions\ShippingExceptions\InvalidParamCalculateException;
use src\Exceptions\ShippingExceptions\UnableToCalculateShipping;
use src\Tools\StringTools;

class ShippingCorreiosBO
{
    private string $url;
    private string $serviceCode;
    private string $zipCodeOrigin;
    private string $zipCodeDestination;
    private int|float $grossWeight;
    private int $length;
    private int $height;
    private int $width;

    public function __construct($originZipCode)
    {
        $this->url = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx';
        $this->zipCodeOrigin = StringTools::onlyNumbers($originZipCode);
    }

    public function calculateShipping(ShippingPackageDTO $package, string $zipCodeDestination): array
    {
        $this->prepareCalculation($package, $zipCodeDestination);
        $this->validateParamsForCalculation();
        $codes = array(ShippingCorreiosEnum::PAC, ShippingCorreiosEnum::SEDEX);
        $calculation = array();
        foreach ($codes as $code) {
            $this->serviceCode = $code;
            $params = http_build_query($this->constructParamsToCalculate());
            $curl = curl_init($this->url . '?' . $params);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $calc = curl_exec($curl);
            $calc = simplexml_load_string($calc);
            if ($calc->cServico->Erro != 0 && $calc->cServico->Erro != 11) {
                throw new UnableToCalculateShipping($calc->cServico->MsgErro);
            }
            $calculation[$code] = $calc->cServico;
        }
        return $calculation;
    }

    private function prepareCalculation(ShippingPackageDTO $package, string $zipCodeDestination): void
    {
        $this->zipCodeDestination = StringTools::onlyNumbers($zipCodeDestination);
        $this->grossWeight = $package->getGrossWeight();
        $this->length = $package->getLength();
        $this->height = $package->getHeight();
        $this->width = $package->getWidth();
    }

    private function validateParamsForCalculation(): void
    {
        $this->validateLength();
        $this->validateWidth();
        $this->validateHeight();
        $this->validateSumDimension();
        $this->validateGrossWeight();
        $this->validateDestinationZipCode();
    }

    private function validateLength(): void
    {
        if ($this->length < 15) {
            $this->length = 15;
        }
        $this->validateMaxDimension($this->length);
    }

    private function validateWidth(): void
    {
        if ($this->width < 10) {
            $this->width = 10;
        }
        $this->validateMaxDimension($this->width);
    }

    private function validateHeight(): void
    {
        if ($this->height < 2) {
            $this->height = 2;
        }
        $this->validateMaxDimension($this->height);
    }

    private function validateGrossWeight(): void
    {
        if ($this->grossWeight < 0.300) {
            $this->grossWeight = 0.300;
        }
        if ($this->grossWeight > 30) {
            throw new InvalidParamCalculateException('O peso máximo aceito é de 30 KG!');
        }
    }

    private function validateSumDimension(): void
    {
        $dimensionSum = $this->height + $this->width + $this->length;
        if ($dimensionSum > 200) {
            throw new InvalidParamCalculateException(
                'A soma das dimensões não pode ultrapassar 200 centímetros!'
            );
        }
    }

    private function validateMaxDimension(int $dimension)
    {
        if ($dimension > 100) {
            throw new InvalidParamCalculateException(
                'Dimensão maior que o permitido, nenhuma dimensão deve ultrapassar 100 centímetros!'
            );
        }
    }

    private function validateDestinationZipCode(): void
    {
        if (strlen($this->zipCodeDestination) != 8) {
            throw new InvalidParamCalculateException('CEP de destino inválido!');
        }
    }

    private function constructParamsToCalculate(): array
    {
        return array(
            'nCdEmpresa' => '',
            'sDsSenha' => '',
            'sCepOrigem' => $this->zipCodeOrigin,
            'sCepDestino' => $this->zipCodeDestination,
            'nVlPeso' => $this->grossWeight,
            'nCdFormato' => ShippingCorreiosEnum::SEND_FORMAT_BOX,
            'nVlComprimento' => $this->length,
            'nVlAltura' => $this->height,
            'nVlLargura' => $this->width,
            'nVlDiametro' => '0',
            'sCdMaoPropria' => 'n',
            'nVlValorDeclarado' => '0',
            'sCdAvisoRecebimento' => 'n',
            'StrRetorno' => 'xml',
            'nCdServico' =>  $this->serviceCode
        );
    }
}