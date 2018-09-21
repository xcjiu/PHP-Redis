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


}
