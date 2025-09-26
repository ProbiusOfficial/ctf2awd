#!/bin/bash

flag=$(cat /flag)

output=$(curl -s http://localhost/shell.php)

if [[ $output == "horse_is_running" ]]; then
    echo "丸辣，不死马在你机器上下崽了，马儿还在跑，你怎么还不跑"
else
    echo "应急成功，这是你的flag：$flag"
fi