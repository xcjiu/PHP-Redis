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



- ### $redis::expire($key, $expire=0);
### 设置过期时间(秒)

参数说明：（参数名，类型，[中文说明]）

$key  string | int  [ 键名]

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



- ### $redis::mset($arr);

### 同时设置一个或多个键值对。（支持键值为数组）

参数说明：

$arr array  [要设置的键值对数组]

return  bool  [返回布尔值，成功true否则false]
#



- ### $redis::mget($args)

### 返回所有(一个或多个)给定 key 的值

参数说明：

可传入一个或多个键名参数，键名字符串类型，如 $values = $redis::mget('one','two','three', ...);

返回包含所有指定键值的数组，如果值不存在则返回false
#



- ### $redis::expiretime($key);
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



- ### $redis::valuelen($key);
### 返回字符串的长度，如果键值是数组则返回数组元素的个数

参数说明：

$key  string | int [键名]

return  int  [返回长度值，如果键值不存在则返回0]
#



- ### $redis::inc($key, $int=0);
### 将 key 中储存的数字值自增

参数说明：

$key  string | int [键名]

$int  int [$int 自增量，如果不填则默认是自增量为 1]

return  int | bool [返回自增后的值，如果键不存在则新创建值为0并返回自增后的数值.如果键值不是可转换的整数，则返回false]
#



-### $redis::dec($key, $int=0);
### 将 key 中储存的数字值自增

参数说明：

$key  string | int [键名]

$int  int [$int 自减量，如果不填则默认是自减量为 1]

return  int | bool [返回自减后的值，如果键不存在则新创建值为0并返回自减后的数值.如果键值不是可转换的整数，则返回false]
#



- ### $redis::append($key, $value, $pos=false, $expire=0);
### 为指定的 key 追加值(追加至末尾或开头位置，支持数组值追加)

参数说明：

$key  string | int [键名]

$value  string | array   [要指定的键值]

$pos  bool  [追加的位置，默认false为末尾，true为向开头位置追加]

$expire  int   [过期时间，如果不设置则用全局过期配置]

return  bool  [设置成功返回true否则false，向字符串值追加时加入的值必须为字符串类型。如果键不存在则创建新的键值对]
#



- ### $redis::hset($table, $column, $value, $expire=0);
### 为哈希表中的字段赋值 

参数说明：

$table  string  [哈希表名]

$column  string  [字段名]

$value  string | array  [字段值，如果传入的是数组则自动转换为json字符串]

$expire  int  [过期时间，如果不填，默认0为不设置过期时间]

return int  如果成功返回 1，否则返回 0.当字段值已存在时覆盖旧值并且返回 0   
#



- ### $redis::hget($table, $column);
### 获取哈希表字段值

参数说明：

$table  string  [哈希表名]

$column  string  [字段名]

return string  返回字段值，如果字段值是数组保存的返回json格式字符串，转换成数组json_encode($value),如果字段不存在返回false
#


- ### $redis::hdel($table, $column1, $column2, ....);
### 删除哈希表 key 中的一个或多个指定字段，不存在的字段将被忽略（删除整个哈希表用$redis::del($table)）

参数说明：

$table  string  [哈希表名]

$column  string  [字段名]

return int  返回被成功删除字段的数量，不包括被忽略的字段,(删除哈希表用$redis::del($table))
#



- ### $redis::hexists($table, $column);

参数说明：

$table  string  [哈希表名]

$column  string  [字段名]

return bool  存在返回true,否则false
#


- ### $redis::hgetall($table);
### 返回哈希表中，所有的字段和值(键值对数组)

参数说明：

$table  string  [哈希表名]

return array 返回键值对数组
#


- ### $redis::hinc($table, $column, $num=1);
### 为哈希表中的字段值加上指定增量值(支持整数和浮点数)

参数说明：

$table  string  [哈希表名]

$column  string  [字段名]

$num  int  [增量值，默认1，也可以填入负数值,相当于对指定字段进行减法操作]

return  int|float|bool  返回计算后的字段值,如果字段值不是数字值则返回false,如果哈希表不存在或字段不存在返回false
#


- ### $redis::hkeys($table);
### 获取哈希表中的所有字段

参数说明：

$table  string  [哈希表名]

return array 返回包含所有字段的数组
#


- ### $redis::hvals($table);
### 获取哈希表中的所有字段值

参数说明：

$table  string  [哈希表名]

return array 返回包含所有字段值的数组，数字索引
#



- ### $redis::hlen($table);
### 获取哈希表中字段的数量

参数说明：

$table  string  [哈希表名]

return int 返回字段数量，如果哈希表不存在则返回0
#


-### $redis::hmget($table, $column1, $column2, ....);
### 获取哈希表中，一个或多个给定字段的值

参数说明：

$table  string  [哈希表名]

$columns string [字段名，可传多个]

return array 返回键值对数组，如果字段不存在则字段值为null, 如果哈希表不存在返回空数组
#


- ### $redis::hmset($table, array $data, $expire=0);
### 同时将多个 field-value (字段-值)对设置到哈希表中

参数说明：

$table  string  [哈希表名]

$data array [要添加的键值对]

$expire int [过期时间，默认值0或不填则不设置过期时间]

return bool 成功返回true,否则false
#


- ### $redis::hsetnx($table, $column, $value, $expire=0);
### 为哈希表中不存在的的字段赋值

参数说明：

$table  string  [哈希表名]

$column string [字段名]

$value mix [字段值]

$expire int [过期时间，默认值0或不填则不设置过期时间]

return bool 成功返回true,否则false
#






















