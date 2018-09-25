# PHP-Redis 扩展应用类 
**（开始在 PHP 中使用 Redis 前， 请确保已经安装了 redis 服务及 PHP redis 驱动）**
### 这是一个Redis应用类，所有方法均使用静态调用
#### 实例化类配置参数说明：$redis = new redis\Redis($config);
```
$config = [
    'host' => '127.0.0.1',  //服务器连接地址。默认='127.0.0.1'
    'port' => '6379',  //端口号。默认='6379'
    'expire' => 3600,  // 默认全局过期时间，单位秒。不填默认3600
    'password' => '',  // 连接密码，如果有设置密码的话
    'db' => '',   //缓存库选择。默认0
    'timeout' => 10  // 连接超时时间（秒）。默认10
];

```
如果不需要更改默认参数值的话，不需要传入任何参数：$redis = new redis\Redis();

如果你放在框架里用（或有自动加载机制），直接命名空间引入即可用，不需要实例化类！

use redis\Redis; 就可直接静态调用了

如thinkphp框架，把文件夹redis放入extend目录下，在控制器中使用如：
```
<?php
namespace app\index\controller;
use redis\Redis;
class Index
{
    public function index()
    {
        ........
        Redis::set('key', 'value', 7200);
        $res = Redis::get('key');
        ........
    }
}
```
## 具体方法说明如下：（以下$redis变量表示Redis对象）
- ### $redis::set($key, $value, $expire=0); 

### 存储一个键值 （支持数组、对象）

参数说明：（参数名，类型，[中文说明]）

$key  string | int  [ 键名]

$value  mix  [要存储的值，支持数组、对象]

$expire  int  [过期时间（秒），如果使用全局过期时间配置，可以不填]

return  bool  [返回布尔值，成功true, 否则false]
#
- ### $redis::get($key);

### 获取一个键值

参数说明：

$key  string | int [键名]

return  mix  [返回键值，如果键不存在则返回false]
#
- ### $redis::del($key);

### 删除一个键值

参数说明：

$key  string | int [键名]

return  mix  [删除成功返回 1，删除失败或键不存在返回 0]
#
- ### $redis::substr($key, $start, $end=0);

### 截取缓存字符串值（支持汉字）

参数说明：

$key  string | int [键名]

$start  int   [起始位置，从0开始记]

$end  int   [截取长度，默认值0表示截取从起始位置到最后一个字符]

return   string  [返回字符串，如果键不存在或取值不是字符串类型则返回 false]
#
- ### $redis::replace($key, $value, $expire=0);

### 设置指定 key 的值，并返回 key 的旧值（支持数组）

参数说明：

$key  string | int [键名]

$value  mix   [要指定的键值]

$expire  int   [过期时间，如果不设置则用全局过期配置]

return   mix  [返回旧值,如果旧值不存在则返回false,并新创建key的键值]
#
- ### $redis::mget()

### 返回所有(一个或多个)给定 key 的值

参数说明：

可传入一个或多个键名参数，键名字符串类型，如 $values = $redis::mget('one','two','three', ...);

返回包含所有指定键值的数组，如果值不存在则返回false
#
- ### $redis::expireTime($key);
### 查询剩余过期时间（秒）

参数说明：

$key string | int  [键名]

return  int  [返回剩余的存活时间（秒），如果已过期则返回负数]
#
- ### $redis::setnx($key, $value, $expire=0);
### 指定的 key 不存在时，为 key 设置指定的值(SET if Not eXists)

参数说明：

$key  string | int [键名]

$value  mix   [要指定的键值]

$expire  int   [过期时间，如果不设置则用全局过期配置]

return  bool  [设置成功返回true否则false]
#









