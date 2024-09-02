#!/bin/bash

# MySQL 连接信息
DB_USER="root"
DB_PASS="root"
DB_NAME="ctf"

# 从/flag文件获取flag
FLAG=$(cat /flag)

# 从环境变量中获取最初生成的随机值
EXPECTED_RANDOM="$RANDOM"

# 清空数据库
echo "准备好了是吧，那么 RM -rf / 删库，跑路！！(雾"
mysql -u"$DB_USER" -p"$DB_PASS" -e "DROP DATABASE IF EXISTS $DB_NAME; CREATE DATABASE $DB_NAME;" 2>/dev/null

# 通知用户数据库已清空，并开始倒计时
echo "哦豁，掉库辣！广快还原！"

# 休眠 5 分钟
sleep 300  # 300 秒 = 5 分钟

# 重新连接到数据库并检查恢复情况
echo "时间差不多咯~"

# 从数据库中获取存储的随机值
RESULT=$(mysql -u"$DB_USER" -p"$DB_PASS" -e "
USE $DB_NAME;
SELECT value_text FROM metadata WHERE key_name = 'flag';
" 2>/dev/null | tail -n 1)

# 验证结果
if [[ "$RESULT" == "DatabaseIntegrityCheckFlag{$EXPECTED_RANDOM}" ]]; then
    echo "Congratulations! Database restored successfully. Here is your flag: $FLAG"
else
    echo "丸辣，库没辣，提桶跑路吧你！！"
    exit 1
fi
