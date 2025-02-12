<?php

declare(strict_types=1);

namespace MatanYadaev\EloquentSpatial\Objects;

class Point extends Geometry
{
    public float $latitude;

    public float $longitude;

    public function __construct(float $latitude, float $longitude, ?int $srid = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->setDefaultSrid($srid);
    }

    public function toWkt(bool $withFunction = true): string
    {
        $wkt = "{$this->longitude} {$this->latitude}";

        if ($withFunction) {
            return "POINT({$wkt})";
        }

        return $wkt;
    }

    /**
     * @return array{0: float, 1: float}
     */
    public function getCoordinates(): array
    {
        return [
          $this->longitude,
          $this->latitude,
        ];
    }
}
