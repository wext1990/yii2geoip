<?php

namespace wext\yii2geoip;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\caching\CacheInterface;
use wext\yii2geoip\models\City as CityModel;

class GeoipReader extends Component implements GeoipReaderInterface
{
    public $cache = 'cache';
    public $defaultCacheDuration = 3600;
    
    protected $dbReader = 'wext\yii2geoip\PeclGeoipReader';
    protected $cacheProxy;
    
    public function getDbReader(): GeoipReaderInterface
    {
        if (!($this->dbReader instanceof GeoipReaderInterface)) {
            $this->dbReader = Yii::createObject($this->dbReader);
        }
        return $this->dbReader;
    }
    
    public function setDbReader($reader)
    {
        $this->dbReader = $reader;
    }
    
    public function cache($duration = null, $dependency = null): GeoipReaderInterface
    {
        if (!$this->cacheProxy) {
            $this->cacheProxy = new CacheGeoipReader($this->getDbReader(), $this->cache);
        }
        
        return $this->cacheProxy->cache($duration ?? $this->defaultCacheDuration, $dependency);
    }
    
    public function city(string $ip): CityModel
    {
        return $this->getDbReader()->city($ip);
    }
}
