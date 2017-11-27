<?php

namespace wext\yii2geoip;

use wext\yii2geoip\models\City as CityModel;

interface GeoipReaderInterface
{
    public function city(string $ip): CityModel;
}
