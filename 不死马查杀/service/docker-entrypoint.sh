#!/bin/bash

# 删除自身脚本以增强安全性
rm -f /docker-entrypoint.sh

# 启动MySQL服务
mysqld_safe &

# 检查MySQL是否已启动的函数
mysql_ready() {
    mysqladmin ping --socket=/run/mysqld/mysqld.sock --user=root --password=root > /dev/null 2>&1
}

# 等待MySQL启动
while !(mysql_ready)
do
    echo "waiting for mysql ..."
    sleep 3
done

# Check the environment variables for the flag and assign to INSERT_FLAG
if [ "$DASFLAG" ]; then
    INSERT_FLAG="$DASFLAG"
    export DASFLAG=no_FLAG
    DASFLAG=no_FLAG
elif [ "$FLAG" ]; then
    INSERT_FLAG="$FLAG"
    export FLAG=no_FLAG
    FLAG=no_FLAG
elif [ "$GZCTF_FLAG" ]; then
    INSERT_FLAG="$GZCTF_FLAG"
    export GZCTF_FLAG=no_FLAG
    GZCTF_FLAG=no_FLAG
else
    INSERT_FLAG="flag{TEST_Dynamic_FLAG}"
fi


# 创建数据库校验标识
random_flag=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 32 | head -n 1)

mysql -u root -proot -e "
USE ctf;
INSERT INTO metadata (key_name, value_text) VALUES ('flag', 'DatabaseIntegrityCheckFlag{$random_flag}');
"

echo $random_flag > /var/log/DB_FLAG

# Write the flag to a file (update path as needed)
echo $INSERT_FLAG | tee /flag

# Set permissions for the flag file
chmod 744 /flag

# 启动PHP-FPM和Nginx服务
php-fpm & nginx &

# 启动SSH服务
service ssh start

echo "Running..."

# 保持容器运行并输出Nginx日志
tail -F /var/log/nginx/access.log /var/log/nginx/error.log



