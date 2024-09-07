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

--- HelloCTF - ctf2awd靶场 : Just a Linux --- 

```
「 攻防模式 | AWD (Attack With Defense) 」 是 CTF比赛 「CTF Capture The Flag」 几种主要的比赛模式之一，该模式常见于线下赛。

在该模式中，每个队伍都拥有一个相同的初始环境 ( 我们称其为 GameBox )，该环境通常运行着一些特定的服务或应用程序，而这些服务通常包含一些安全漏洞。参赛队伍需要挖掘利用对方队伍服务中的安全漏洞，获取 Flag 以获得积分; 同时，参赛队伍也需要修补自身服务漏洞进行防御，以防被其他队伍攻击和获取 Flag。

接下来开启题目来获取你的 GameBox 吧！！

## 配置信息
docker - ubuntu:22.04
username: `helloctf`
password: `123456`

开启题目后会得到 `IP:PORT` 信息，这里将不再能使用浏览器直接打开，你需要使用 `ssh` 连接到 GameBox 上:

```bash
ssh username@ip -p port
```

内部端口在无特殊声明的情况下均为 22 端口，由于存在 MySQL 环境，最低内存请给256，建议512。

注意：在开启该部分容器时，尽可能多分配使用内存，否则可能会导致一些功能无法正常使用。

如果你不了解 Linux ，可以点击该链接：https://www.runoob.com/linux/linux-tutorial.html

or https://www.zintis.net/