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

--- HelloCTF - ctf2awd靶场 : 信息明确 --- 

```

## 配置信息
docker - ubuntu:22.04
username: `helloctf`
password: `123456`

开启题目后会得到 `IP:PORT` 信息，这里将不再能使用浏览器直接打开，你需要使用 `ssh` 连接到 GameBox 上:

```bash
ssh username@ip -p port
```

## 关于本关卡

> 《孙子兵法 · 谋攻》：「知彼知己，百战不殆。」


### 明确Linux机器信息
```bash
uname -a                       # 系统信息
ps -aux                        # 查询进程信息
ps -ef | grep <name>           # 筛选指定进程
id                             # 用于显示用户ID，以及所属群组ID
cat /etc/passwd                # 查看用户情况
ls /home/                      # 查看用户情况
find / -type d -perm -002      # 可写目录检查
ifconfig                       # Linux上查看网卡信息
ip addr show                   # Linux上查看网卡信息
```

### 查看开放端口
```bash
netstat          # 查看活动连接
netstat -ano/-a  # 查看端口情况
netstat -anp     # 查看端口
```

### 检查防火墙类型
```bash
/*注意在该容器下防火墙可能不可用*/
ufw status             # 检查 ufw 状态
iptables -L            # 检查 iptables 规则
nft list ruleset       # 检查 nftables 规则
firewall-cmd --state   # 检查 firewalld 状态
```

### 中间件信息
```bash
find / -name "nginx.conf"                  #定位nginx目录 
# 由于环境限制 可能会有较多权限不允许的目录 可以加上 2>/dev/null 过滤
find / -path "*nginx*" -name nginx*conf    #定位nginx配置目录
find / -name "httpd.conf"                  #定位apache目录
find / -path "*apache*" -name apache*conf  #定位apache配置目录
```

### 相关配置文件
> 通过翻找web应用程序工作目录(通常为 /var/www/html 或 /app)来查找配置文件,以确定数据库相关配置。

```bash
find / -name "config.php"  #查找配置文件
```
而后通过文件中相关变量名进行查找，如数据库密码为 DB_PASSWORD，则可以使用：
```bash
grep -r "DB_PASSWORD" /path/maybe/have/config
```

### 日志信息

> 对日志的实时捕捉，除了能有效提升防御以外，还能捕捉攻击流量，得到一些自己不清楚的攻击手段，平衡攻击方和防守方的信息差。

```bash
/var/log/nginx/        #默认Nginx日志目录
/var/log/apache/       #默认Apache日志目录
/var/log/apache2/      #默认Apache日志目录
/usr/local/tomcat/logs #Tomcat日志目录
tail -f xxx.log        #实时刷新滚动日志文件
```
### Flag位置

> 注意，目前很多awd靶机不再使用flag文件，而是转为通过 Curl 类似的命令向 FlagServer 发送带有队伍标识的请求来获得加分。

```bash
grep -r "flag" /path/maybe/have/flag
```