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

--- HelloCTF - ctf2awd靶场 : 一般加固 - 口令更改 / 源码备份和权限配置 --- 

```

## 配置信息
docker - ubuntu:22.04
username: `helloctf`
password: `123456`

开启题目后会得到 `IP:PORT` 信息，这里将不再能使用浏览器直接打开，你需要使用 `ssh` 连接到 GameBox 上:

```bash
ssh username@ip -p port
```

## 本关卡信息

### PING 禁用

目前大多数 AWD 比赛无需选手再做靶机(GameBox)探测，而是会直接告知主机列表，所以该操作不一定存有意义。

```bash
echo "1" > /proc/sys/net/ipv4/icmp_echo_ignore_all     # 临时开启禁ping
echo "0" > /proc/sys/net/ipv4/icmp_echo_ignore_all     # 关闭禁ping
```

### 口令更改

这里需要更改的口令包括但不限于服务器 SSH 口令、数据库口令，WEB 服务口令以及 WEB 应用后台口令。

```bash
passwd username                                     # ssh口令修改
set password for mycms@localhost = password('123'); # MySQL密码修改(注意为SQL语句)
find /var/www/html -path '*config*'                # 查找配置文件中的密码凭证
```

注意：更改数据库口令可能会影响网站的正常运行，为了避免宕机处罚，请确保修改同步进行。


### 文件备份建立

除了攻击成功可以让对手扣分，还能破坏对方环境使其宕机被 check 扣分；同时己方也有可能在修复过程中存在一些误操作，导致源码出错，致使服务停止；对页面快速恢复时，及时备份是必要的，因此页面备份至关重要。

```bash
# 压缩
tar -cvf web.tar /var/www/html
zip -q -r web.zip /var/www/html
# 解压
tar -xvf web.tar -c /var/www/html
unzip web.zip -d /var/www/html
```

备份文件请存放在非web目录下，同时保存一份到自己的主机上，目前大多数ssh软件拥有文件管理的功能，可以直接下载文件到本地，但也请记住下方命令以应对突发情况：

```bash
scp username@servername:/path/filename /tmp/local_destination  # 从服务器下载单个文件到本地
scp /path/local_filename username@servername:/path             # 从本地上传单个文件到服务器
scp -r username@servername:remote_dir/ /tmp/local_dir          # 从服务器下载整个目录到本地
scp -r /tmp/local_dir username@servername:remote_dir           # 从本地上传整个目录到服务器
```

## 通关要求

- 无伤修改 SSH口令、数据库口令（即修改未触发宕机惩罚），(WEB应用程序后台口令*:懒了，没做后台w，可以无视这点)。

- 打包源码文件，命名为web.tar，将其放置到 /home/helloctf/ 目录下。

- 完成后在终端输入 check.sh 以获取 flag。