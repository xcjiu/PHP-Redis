<?php
namespace redis;
use \Redis as RedisBase;
// +-----------------------------------------------------
// | Redis扩展类
// +-----------------------------------------------------
// | author: xcjiu
// +-----------------------------------------------------
// | github: https://github.com/xcjiu/PHP-Redis
// +-----------------------------------------------------
class Redis
{
    private static $redis = null;
    private static $expire = 3600; //默认存储时间（秒）
    private static $host = '127.0.0.1';
    private static $port = '6379';
    private static $password = '';
    private static $db = '';
    private static $timeout = 10;
    /**
     * 初始化Redis连接
     * 所有配置参数在实例化Redis类时加入参数即可
     */
    public function __construct($config=[])
    {
        if($config && is_array($config)){
            self::config($config);
        }
        if(self::$redis==null){
            self::$redis = new RedisBase();
        }
        self::$redis->connect(self::$host,self::$port,self::$timeout) or die('Redis 连接失败！');
        if(!empty(self::$password)){
            self::$redis->auth(self::$password); //如果有设置密码，则需要连接密码
        }
        if(!empty(self::$db)){
            self::$redis->select(self::$db); //选择缓存库
        }
    }

    /**
     * 加载配置参数
     * @param  array  $config 配置数组
     */
    private static function config(array $config=[])
    {
        self::$host = isset($config['host']) ? $config['host'] : '127.0.0.1'; 
        self::$port = isset($config['port']) ? $config['port'] : '6379'; 
        self::$password = isset($config['password']) ? $config['password'] : ''; 
        self::$db = isset($config['db']) ? $config['db'] : ''; 
        self::$expire = isset($config['expire']) ? $config['expire'] : 3600; 
        self::$timeout = isset($config['timeout']) ? $config['timeout'] : 10; 
    }

    /**
     * 存储一个键值
     * @param  string or int $key 键名
     * @param  mix $value 键值，支持数组、对象
     * @param  int $expire 过期时间(秒)
     * @return bool 返回布尔值
     */
    public static function set($key, $value, $expire='')
    {
        if(is_int($key) || is_string($key)){
            //如果是int类型的数字就不要序列化，否则用自增自减功能会失败，
            //如果不序列化，set()方法只能保存字符串和数字类型,
            //如果不序列化，浮点型数字会有失误，如13.6保存，获取时是13.59999999999
            $value = is_int($value) ? $value : serialize($value);
            $expire = (int)$expire ? $expire : self::$expire;
            if(self::$redis->set($key, $value) && self::$redis->expire($key, $expire)){
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * 设置过期时间
     * @param  string or int $key 键名
     * @param  int $expire 过期时间(秒)
     * @return bool 返回布尔值  [如果成功返回true,如果键不存在或已过期则返回false]
     */
    public static function expire($key, $expire=0)
    {
        $expire = (int)$expire ? $expire : self::$expire;
        if(self::$redis->expire($key, $expire)){
            return true;
        }
        return false;
    }

    /**
     * 获取键值
     * @param string or int $key 键名
     * @return mix 返回值
     */
    public static function get($key)
    {
        $value = self::$redis->get($key);
        return is_numeric($value) ? $value : unserialize($value);
    }

    /**
     * 删除一个键值
     * @param  string or int $key 键名
     * @return int 成功返回1 ，失败或不存在键返回0
     */
    public static function del($key)
    {
        return self::$redis->del($key);
    } 

    /**
     * 截取字符串,支持汉字
     * @param  string or int $key 键名
     * @param  int $start 起始位，从0开始
     * @param  int $end   截取长度
     * @return string   返回字符串,如果键不存在或值不是字符串类型则返回false
     */
    public static function substr($key,$start,$end=0)
    {   
        $value = self::get($key);
        if($value && is_string($value)){
           if($end){
                return mb_substr($value,$start,$end);
            }
            return mb_substr($value,$start); 
        }
        return false;
    }

    /**
     * 设置指定 key 的值，并返回 key 的旧值
     * @param  string or int  $key 键名
     * @param  mix  $value 要指定的健值，支持数组
     * @param  int $expire 过期时间，如果不填则用全局配置
     * @return mix 返回旧值，如果旧值不存在则返回false,并新创建key的键值
     */
    public static function replace($key, $value, $expire=0)
    {
        $value = self::$redis->getSet($key, $value);
        $expire = (int)$expire ? $expire : self::$expire;
        self::$redis->expire($key, $expire);
        return is_numeric($value) ? $value : unserialize($value);
    }

    /**
     * 同时设置一个或多个键值对。（支持数组值）
     * @param  array  $arr [要设置的键值对数组]
     * @return bool 返回布尔值，成功true否则false
     */
    public static function mset($arr)
    {
        if($arr && is_array($arr)){
            foreach ($arr as &$value) {
               $value = is_int($value) ? $value : serialize($value);
            }
            if(self::$redis->mset($arr)){
               return true; 
            }
           return false; 
        }
        return false;
    }

    /**
     * 返回所有(一个或多个)给定 key 的值
     * 可传入一个或多个键名参数，键名字符串类型，如 $values = $redis::mget('one','two','three', ...);
     * @return 返回包含所有指定键值数组，如果不存在则返回false
     */
    public static function mget()
    {
        $keys = func_get_args();
        if($keys){
            $values = self::$redis->mget($keys);
            if($values){
                foreach ($values as &$value) {
                    $value = is_numeric($value) ? $value : unserialize($value);
                }
                return $values;
            }
        }
        return false;
    }

    /**
     * 查询剩余过期时间（秒）
     * @param  string or int $key  键名
     * @return int 返回剩余的时间，如果已过期则返回负数
     */
    public static function expiretime($key) 
    {
        return self::$redis->ttl($key);
    }

    /**
     * 指定的 key 不存在时，为 key 设置指定的值(SET if Not eXists)
     * @param  string or int $key  键名
     * @param  mix  $value 要指定的健值，支持数组
     * @param  int $expire 过期时间，如果不填则用全局配置
     * @return bool  设置成功返回true 否则false
     */
    public static function setnx($key, $value, $expire=0)
    {
        $value = is_int($value) ? $value : serialize($value);
        $res = self::$redis->setnx($key, $value);
        if($res){
            $expire = (int)$expire ? $expire : self::$expire;
            self::$redis->expire($key, $expire);
        }
        return $res;
    }

    /**
     * 返回对应键值的长度
     * @param  string or int $key  键名
     * @return int  返回字符串的长度，如果键值是数组则返回数组元素的个数，如果键值不存在则返回0
     */
    public static function valuelen($key)
    {
        $value = self::get($key);
        $lenth = 0;
        if($value){
            if(is_array($value)){
                $lenth = count($value);
            }else{
               $lenth = strlen($value);
           }
        }
        return $lenth;
    }

    /**
     * 将 key 中储存的数字值自增
     * @param  string or int $key  键名
     * @param  int $int 自增量，如果不填则默认是自增量为 1
     * @return int  返回自增后的值，如果键不存在则新创建一个值为0，并在此基础上自增，返回自增后的数值.如果键值不是可转换的整数，则返回false
     */
    public static function inc($key, $int=0)
    {
        if((int)$int){
            return self::$redis->incrby($key,$int);
        }else{
            return self::$redis->incr($key);
        }
    }

    /**
     * 将 key 中储存的数字值自减
     * @param  string or int $key  键名
     * @param  int $int 自减量，如果不填则默认是自减量为 1
     * @return int  返回自减后的值，如果键不存在则新创建一个值为0，并在此基础上自减，返回自减后的数值.如果键值不是可转换的整数，则返回false
     */
    public static function dec($key, $int=0)
    {
        if((int)$int){
            return self::$redis->decrby($key,$int);
        }else{
            return self::$redis->decr($key);
        }
    }

    /**
     * 为指定的 key 追加值
     * @param  string or int $key  键名
     * @param  mix  $value 要指定的健值，支持数组
     * @param  bool $pos 要追加的位置，默认false为追加至末尾，true则追加到开头
     * @param  int $expire 过期时间，如果不填则用全局配置
     * @return bool  设置成功返回true 否则false,支付向字符串或者数组追加内容，向字符串追加时加入的值必须为字符串类型，如果健不存在则创建新的键值对
     */
    public static function append($key, $value, $pos=false, $expire=0)
    {
        $cache = self::get($key);
        if($cache){
            if(is_array($cache)){
                if($pos===true){
                    $value = array_unshift($cache, $value);
                }else{
                   $value = array_push($cache, $value); 
               }
            }else{
                if(!is_string($value)){
                    return false;
                }
                if($pos===true){
                    $value .= $cache;
                }else{
                    $value = $cache . $value;
                }
            }
        }
        return self::set($key, $value, $expire);
    }
 

    // +--------------------------------------------------
    // | 以上方法均为字符串常用方法，并且把数组也兼容了
    // | 以下为哈希表处理方法
    // +--------------------------------------------------
    

    /**
     * 为哈希表中的字段赋值 
     * @param  string  $table  哈希表名
     * @param  string  $column 字段名
     * @param  string|array  $value  字段值
     * @param  int $expire 过期时间, 如果不填则不设置过期时间
     * @return int  如果成功返回 1，否则返回 0.当字段值已存在时也返回 0  
     */
    public static function hset($table, $column, $value, $expire=0)
    {
        $value = is_array($value) ? json_encode($value) : $value;
        $res = self::$redis->hset($table, $column, $value);
        if((int)$expire){
            self::$redis->expire($table, $expire);
        }        
       return $res;
    }

    public static function hget($table, $column)
    {
        return self::$redis->hget($table, $column);
    }



    public static function myself()
    {
        return self::$redis;
    }
}
