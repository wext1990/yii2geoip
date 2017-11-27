<?php

namespace wext\yii2geoip;

use Yii;
use wext\yii2geoip\models\ModelInterface;

class CacheGeoipReader implements GeoipReaderInterface
{
    protected $reader;
    protected $cache;
    
    protected $duration;
    protected $dependency;
    
    public function __construct(GeoipReaderInterface $reader, $cache)
    {
        if (is_string($cache) && Yii::$app) {
            $cache = Yii::$app->get($cache);
        }
        if (!($cache instanceof CacheInterface)) {
            throw new InvalidConfigException('Invalid cache configuration');
        }
        
        $this->reader = $reader;
        $this->cache = $cache;
    }
    
    public function city(string $ip): CityModel
    {
        return $this->callMethod('city', $ip);
    }
    
    public function cache($duration = null, $dependency = null): CacheGeoipReader
    {
        $this->duration = $duration;
        $this->dependency = $dependency;
        
        return $this;
    }
    
    protected function callMethod(string $db, string $ip): ModelInterface
    {
        $model = $this->cache->getOrSet(
            $this->buildKey($db, $ip),
            function () use ($db, $ip) {
                return call_user_func([$this->reader, $db], $ip);
            },
            $this->duration,
            $this->dependency
        );
        
        $this->duration = null;
        $this->dependency = null;
        
        return $model;
    }
    
    protected function buildKey(string $db, string $ip): string
    {
        return "geoip_{$db}_{$ip}";
    }
}
