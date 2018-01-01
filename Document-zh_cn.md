# OnePort 文档

## 1. Server
> 这里，您将学习OnePort服务器用法。

### 1.1 实例化一个Server
```php
<?php
require 'vendor/autoload.php';

$server = new \CloudSky\OnePort\Server("path/to/config.json");
```
其中，构造函数一共有一个参数，是可选的。
它可以是一个字符串，表示配置文件的路径，也可以是一个数组，包括配置，也可以为空，稍后导入配置。

### 1.2 进行配置
OnePort 有几种进行配置的方式。

#### 1.2.1 使用配置文件
```php
$server->importConfig('path/to/config.json');
```
配置文件须是Json格式的。
#### 1.2.2 运行时导入配置
单条导入：
```php
$server->config('key', 'value');
```
多条导入：
```php
$server->config([
    'key1' => 'value1',
    'key2' => 'value2',
    '...'  => '...'
]);
```
#### 1.2.3 配置项详解
| 名称 | 解释 | 例子 |
| ---- | ---- | ---- |
| name | 本Worker的名称，仅用来标识，可以为空。 | testOnePortWorker |
| count | 启动进程数，一般为CPU线程数 | 3 |
| http.type | Http访问时的handler | jump 或 proxy 或 handle |
| http.param | Http访问时所使用Handler的参数 | jump例子：http://baidu.com/ proxy例子：tcp://127.0.0.1:80 handle例子：/home/oneport/handler.php |

### 1.2.3 配置用户
#### 1.2.3.1 添加用户
```php
$server->addUser('someUser', 'password');
```
#### 1.2.3.2 删除用户
```php
$server->removeUser('someUser');
```
#### 1.2.3.3 修改用户密码
```php
$server->setPassword('someUser', 'password');
```