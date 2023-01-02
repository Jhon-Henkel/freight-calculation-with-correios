<?php

namespace src\DTO;

class ShippingPackageDTO
{
    private int|float $grossWeight;
    private int $length;
    private int $height;
    private int $width;

    /**
     * @return int|float
     */
    public function getGrossWeight(): int|float
    {
        return $this->grossWeight ?? 0;
    }

    /**
     * @param int|float $grossWeight
     */
    public function setGrossWeight(int|float $grossWeight): void
    {
        $this->grossWeight = $grossWeight;
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length ?? 0;
    }

    /**
     * @param int $length
     */
    public function setLength(int $length): void
    {
        $this->length = $length;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height ?? 0;
    }

    /**
     * @param int $height
     */
    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width ?? 0;
    }

    /**
     * @param int $width
     */
    public function setWidth(int $width): void
    {
        $this->width = $width;
    }
}