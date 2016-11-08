<?php

class MNMemcache
{
    private static $memcache;

    public function __construct(){
        self::$memcache=memcache_connect(CACHE_HOST,CACHE_PORT);
    }
    
    public static function write($key,$value,$expiry,$compression=0){
        $key=self::key($key);
        $memcache=@memcache_connect(CACHE_HOST,CACHE_PORT);
        if($memcache===false){
            return false;
        }
        
        if(is_array($value)){
            #$value=serialize($value);
        }
        
        memcache_delete($memcache, $key);
        memcache_add($memcache, $key,$value,$compression,$expiry);

        return true;
    }
    
    public static function read($key){
        $key=self::key($key);
        $memcache=@memcache_connect(CACHE_HOST,CACHE_PORT);
        if($memcache===false){
            return false;
        }
        $data=memcache_get($memcache,$key);
        if($data===false){
           return false; 
        }else{
            $result=@unserialize($data);
            if($result!==false){
                return $result ;
            }else{
                return $data;
            }
        }
    }
    
    public static function readMulti($keys){
        $keys=self::key($keys);
        $m=new Memcached();
        $m->addServer('localhost',11211);
        $data=$m->getMulti($keys);
        foreach($data as $k=>$v){
            $data[$k]=@unserialize($v);
        }
        return $data;
    }
    
    public static function delete($key){
        $key=self::key($key);
        $memcache=@memcache_connect(CACHE_HOST,CACHE_PORT);
        if($memcache===false){
            return false;
        }
      
        memcache_delete($memcache, $key);
    }
    
    public static function key($camelCasedWord){
        if(is_array($camelCasedWord)){
            foreach($camelCasedWord as $key=>$value){
                $camelCasedWord[$key]=CACHE_PREFIX . strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $value));
            }
            return $camelCasedWord;
        }else{
            $result = CACHE_PREFIX . strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $camelCasedWord));
            return $result;
        }
    }
}
?>