<?php

namespace wext\yii2geoip\models;

class City extends AbstractModel
{
    public $ipAddress;
    public $countryName;
    public $countryCode;
    public $cityName;
    public $longitude;
    public $latitude;
}
