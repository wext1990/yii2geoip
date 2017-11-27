<?php

namespace wext\yii2geoip;

use Yii;
use yii\base\Component;
use wext\yii2geoip\exceptions\DriverException;
use wext\yii2geoip\exceptions\AddressNotFoundException;
use wext\yii2geoip\models\City as CityModel;

/**
 * PHP manual: http://php.net/manual/ru/book.geoip.php
 * You must install PHP GeoIP extension from PECL: https://pecl.php.net/package/geoip
 * You can download latest database from: https://dev.maxmind.com/geoip/legacy/geolite/
 * 
 * PHP GeoIP extension installation for CentOS:
 * 
 * ```bash
 * yum install GeoIP-data
 * yum install php-pecl-geoip
 * ```
 */
class PeclGeoipReader extends Component implements GeoipReaderInterface
{
    public $dbDirectory;
    
    protected $cityDbAvailable;
    
    public function init()
    {
        if (!extension_loaded('geoip')) {
            throw new DriverException('Missing php-pecl-geoip driver');
        }
        
        if ($this->dbDirectory) {
            if (!function_exists('geoip_setup_custom_directory')) {
                throw new DriverException('You must update php-pecl-geoip to version 1.1.0 or greater');
            }
            geoip_setup_custom_directory(Yii::getAlias($this->dbDirectory));
        }
        
        $this->cityDbAvailable = geoip_db_avail(GEOIP_CITY_EDITION_REV0) || geoip_db_avail(GEOIP_CITY_EDITION_REV1);
    }
    
    public function city(string $ip): CityModel
    {
        if (!$this->cityDbAvailable) {
            throw new DriverException('Missing GeoIP city database');
        }
        
        $data = geoip_record_by_name($ip);
        if ($data === false) {
            throw new AddressNotFoundException("IP address {$ip} not found in city database");
        }
        
        return new CityModel([
            'ipAddress' => $ip,
            'countryName' => (string) $data['country_name'],
            'countryCode' => (string) $data['country_code'],
            'cityName' => (string) $data['city'],
            'longitude' => (float) $data['longitude'],
            'latitude' => (float) $data['latitude'],
        ]);
    }
}
