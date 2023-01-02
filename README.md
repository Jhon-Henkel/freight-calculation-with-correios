# Integração de cálculo de frete com os correios
O objetivo desse repositório é entregar uma biblioteca de cálculo de frete com os correios em PHP 8.1.

## Como instalar
Basta fazer a instalação via composer com o seguinte comando:
```
composer require correios/correios-calculate
```

## Como usar:
Para usar essa biblioteca deverá primeiramente montar um objeto de cálculo da seguinte forma:
```
$package = ShippingPackageDTO();
$package->setGrossWeight(2);
$package->setWidth(10);
$package->setHeight(10);
$package->setLength(10);
```
Os parâmetros são:
- grossWeight: deve ser do tipo inteiro ou float, sempre deve ser calculado em quilos;
- width: deve ser do tipo inteiro;
- height: deve ser do tipo inteiro;
- length: deve ser do tipo inteiro;

Todos os parâmetros são obrigatórios e após ter o objeto do pacote contado, basta instanciar e chamar da seguinte forma:
```
$correios = new ShippingCorreiosBO('88790-000');
$calc = $correios->calculateShipping($package, '88750-000');
```
- Na variável calc irá retornar um array com chave os códigos de servido do PAC e do SEDEX.
- Ao instanciar o ShippingCorreiosBO deve-se passar o CEP de origem.
- Ao fazer o cálculo, deve-se passar o objeto do pacote e o CEP de destino.
- É obrigatório informar o objeto de pacote e os CEP's de origem e destino.
---
Os parâmetros fixos passados para os correios nessa biblioteca são:
```
'nCdEmpresa' => '',
'sDsSenha' => '',
'nCdFormato' => 1,
'nVlDiametro' => '0',
'sCdMaoPropria' => 'n',
'nVlValorDeclarado' => '0',
'sCdAvisoRecebimento' => 'n',
'StrRetorno' => 'xml',
```
Em um futuro talvez posso mudar isso para ser mais dinâmico.

Obs.: O coverage dessa biblioteca está em 100% atualmente.
