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

/*不用配置所有的参数，只需要配置和默认配置不同的参数即可，如：
$config = [
    'password' => '123456',
    'expire' => 7200
];*/

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
### 查看哈希表的指定字段是否存在

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


- ### $redis::lpush($list, $value, $pop='first', $expire=0);
### 将一个或多个值插入到列表头部（值可重复）或列表尾部。如果列表不存在，则创建新列表并插入值将一个或多个值插入到列表头部。如果列表不存在，则创建新列表并插入值

参数说明：

$list  string  [列表名]

$value  string|array [要插入的值，如果要插入多个值请传入多个值的数组]

$pop  string  [要插入的位置，默认first头部,last表示尾部]

$expire int [过期时间，默认值0或不填则不设置过期时间]

return int 返回列表的长度
#


- ### $redis::lindex($list, $index=0); 
### 通过索引获取列表中的元素

参数说明：

$list  string  [列表名]

$index  int 索引位置，从0开始计,默认0表示第一个元素，-1表示最后一个元素索引

return string 返回指定索引位置的元素
#


- ### $redis::lset($list, $index, $value);
### 通过索引来设置元素的值

参数说明：

$list  string  [列表名]

$index  int [索引位置]

$value  string  [要设置的值]

return bool  成功返回true,否则false.当索引参数超出范围，或列表不存在返回false。
#


- ### $redis::lrange($list, $start=0, $end=-1);
### 返回列表中指定区间内的元素

参数说明：

$list  string  [列表名]

$start  int 起始位置，从0开始计,默认0

$end int 结束位置，-1表示最后一个元素，默认-1

return array 返回列表元素数组
#


- ### $redis::llen($list);
### 返回列表的长度

参数说明：

$list  string  [列表名]

return int 返回列表长度
#


- ### $redis::lpop($list, $pop='first');
### 移出并获取列表的第一个元素或最后一个元素（默认第一个元素）

参数说明：

$list  string  [列表名]

$pop string  [移出并获取的位置，默认first第一个元素，设为last则为最后一个元素]

return string|bool 移出并返回列表第一个元素或最后一个元素,如果列表不存在则返回false
#


- ### $redis::lpoppush($list1, $list2);
### 从列表1中弹出最后一个值，将弹出的元素插入到另外一个列表2开头并返回这个元素

参数说明：

$list1  string  [要弹出元素的列表名]

$list2  string  [要接收元素的列表名]

return string|bool 返回被弹出的元素,如果其中有一个列表不存在则返回false
#


- ### $redis::lisert($list, $element, $value, $pop='before');
### 用于在指定的列表元素前或者后插入元素。如果元素有重复则选择第一个出现的位置。当指定元素不存在于列表中时，不执行任何操作

参数说明：

$list  string  [列表名]

$element  string  [指定的元素]

$value  string  [要插入的元素]

return int 返回列表的长度。 如果没有找到指定元素 ，返回 -1 。 如果列表不存在或为空列表，返回 0 。
#


- ### $redis::lrem($list, $element, $count=0);
### 移除列表中指定的元素

参数说明：

$list  string  [列表名]

$element  string  [指定的元素]

$count  int  [要删除的个数，0表示删除所有指定元素，负整数表示从表尾搜索, 默认0]

return int 返回被移除元素的数量。 列表不存在时返回 0 
#


- ### $redis::ltrim($list, $start, $stop);
### 让列表只保留指定区间内的元素，不在指定区间之内的元素都将被删除

参数说明：

$list  string  [列表名]

$start  int  [起始位置，从0开始计]

$stop  int  [结束位置，负数表示倒数第n个位置]

return bool  成功返回true否则false
#


- ### 

























