#!/bin/bash

# 从/flag文件获取flag
FLAG=$(cat /flag)

# 检查 SSH 口令是否已更改
echo "Checking SSH password..."
SSH_USER="helloctf"
SSH_PASS="123456"

if echo "$SSH_PASS" | sshpass ssh -o StrictHostKeyChecking=no "$SSH_USER@localhost" 'exit' 2>/dev/null; then
    echo "还搁这打别人呢，家都快没了|(*′口`)"
    echo "SSH还是弱口令，任务失败x"
    exit 1
else
    echo "SSH password changed successfully."
fi

# 检查数据库口令是否已更改
echo "Checking database password..."
DB_USER="root"
DB_PASS="root"

if mysql -u"$DB_USER" -p"$DB_PASS" -e "exit" 2>/dev/null; then
    echo "你这把大狙我都不知道再架谁(;｀O´)"
    echo "数据库口令未更改，死了！"
    exit 1
else
    echo "Database password changed successfully."
fi

# 检查 web 服务器 index.php 是否返回 "Connection failed!" 确定数据库密码同步配置文件修改
echo "Checking web server..."
URL="http://localhost/index.php"
EXPECTED_OUTPUT="Connection failed!"
ACTUAL_OUTPUT=$(curl -s "$URL")
if [[ "$ACTUAL_OUTPUT" == *"$EXPECTED_OUTPUT"* ]]; then
    echo "Web server check passed."
else
    echo "你就瞎几把乱改吧，一改一个不吱声（*゜ー゜*）"
    echo "网站因为你没同步数据库密码宕机了，你死了！"
    exit 1
fi

# 检查是否已打包源码文件
echo "Checking if web.tar exists in /var..."
if [ -f /var/web.tar ]; then
    echo "web.tar exists in /var."
else
    echo "修改一时爽，还原火葬场，byd不备份，你就等死吧o(*≧▽≦)ツ"
    exit 1
fi

# 如果所有检查都通过
echo "好了好了，你赢了，拿上你的flag滚蛋吧(* ￣︿￣): $FLAG"
