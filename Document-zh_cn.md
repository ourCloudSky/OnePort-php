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
配置文件须是Json格式的。例子：

```json
{
    "name": "abc",
    "count": "3",
    "http.type": "proxy",
    "http.param": "tcp:\/\/127.0.0.1:80",
    "user": {
        "test": {
            "password": "3c9909afec25354d551dae21590bb26e38d53f2173b8d3dc3eee4c047e7ab1c1eb8b85103e3be7ba613b31bb5c9c36214dc9f14a42fd7a2fdb84856bca5c44c2",
            "whitelist": [],
            "blacklist": [],
            "shortcut": []
        }
    },
    "shortcut": []
}
```

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

### 1.3 配置用户
#### 1.3.1 添加用户
```php
$server->addUser('someUser', 'password');
```
#### 1.3.2 删除用户
```php
$server->removeUser('someUser');
```
#### 1.3.3 修改用户密码
```php
$server->setPassword('someUser', 'password');
```
### 1.4 黑白名单
#### 1.4.1 为用户白名单中添加内容：
```php
$server->addWhiteList('someUser', 'tcp://something.let.someuser.see:1234');
```
#### 1.4.2 为用户黑名单中添加内容：
```php
$server->addBlackList('someUser', 'tcp://something.don.t.let.someuser.see:1234');
```
#### 1.4.3 为用户关闭白名单
```php
$server->disableWhiteList('someUser');
```
#### 1.4.4 为用户关闭黑名单
```php
$server->disableBlackList('someUser');
```
#### 1.4.5 一些说明
当黑白名单都开启时，优先使用白名单。

### 1.5 导出配置
```php
$server->exportConfig('path/to/config.json');
```

### 1.6 监听某一端口
```php
$server->listen('0.0.0.0:5280');
```

## 2. Client
> 这里，您将学习 OnePort 客户端的使用。

### 2.1 实例化一个 Client
```php
$client = new \CloudSky\OnePort\Client("127.0.0.1:5280");
```
参数说明：唯一的一个参数是 OnePort 服务端的地址。

### 2.2 使用与不适用SSL
> 目前服务端原生还不支持SSL。
> 默认将不适用SSL。

```php
$client->enableSSL(false);
```
有一个参数，为假则不启用，为真则启用。

### 2.3 登录服务器
OnePort 服务端为了安全，需要用户登陆。

```php
$client->login("someUser", "password");
```

第一个参数是用户名，第二个参数是密码。

### 2.4 导入一个Map
Map 是远程地址与本地地址映射关系的描述文件，是可选的。

一个示例Map：
```json
[
    ["tcp:\/\/10.0.50.23:443", "tcp:\/\/127.0.0.23:443", "tcp"]
]
```
第一个是源地址，第二个是映射到的本机地址，第三个是SSL（一般不需要开启，若设置为“SSL”，则会把SSL先转为普通TCP再传输）。

```php
$client->importMap("path/to/map.json");
```

### 2.5 映射一个地址
```php
//普通地址Map，把tcp://10.0.50.23:22映射到127.0.0.23:22
$client->map("tcp://10.0.50.23:22", "tcp://127.0.0.23:22");

//Alias映射，将Alias “aliasTest” 映射到本地。
$client->map("aliasTest", "tcp://127.0.0.21:2333");
```

### 2.6 导出Map
```php
$client->exportMap("path/to/map.json");
```

## 3. 运行
无论是什么，都记得执行这一句：
```php
\Workerman\Worker::runAll();
```

## 4. 一个用于开发的EchoServer

### 4.1 基本用法
很简单，一句话启动：
```php
$echoserver = new \CloudSky\OnePort\Dev\EchoServer("tcp://127.0.0.1:3245");
```
唯一的一个参数是监听地址，记得也要使用 3 中的方法启动。

### 4.2 自定义Worker
很简单：
```php
$worker = new \Workerman\Worker("tcp://127.0.0.1:3246");
$echoserver->serve($worker);
```
