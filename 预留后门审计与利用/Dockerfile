FROM php:7.2-fpm-buster 

# 制作者信息
LABEL auther_template="CTF-Archives"

# 安装必要的软件包
RUN sed -i 's/deb.debian.org/mirrors.ustc.edu.cn/g' /etc/apt/sources.list  && \
    sed -i 's/security.debian.org/mirrors.ustc.edu.cn/g' /etc/apt/sources.list  && \
    apt-get update && \
    apt-get install --no-install-recommends -y \
    tar nginx bash default-mysql-client default-mysql-server \
    libfreetype6-dev libpng-dev libjpeg-dev openssh-server && \
    rm -rf /var/lib/apt/lists/*

# 配置mysql+gd
RUN docker-php-source extract && \
    docker-php-ext-install iconv && \
    docker-php-ext-install pdo_mysql mysqli

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/freetype2 --with-png-dir=/usr/include --with-jpeg-dir=/usr/include

RUN docker-php-ext-install gd && \
    docker-php-ext-install mbstring && \
    docker-php-source delete

# 配置mysql
RUN mysql_install_db --user=mysql --datadir=/var/lib/mysql && \
    sh -c 'mysqld_safe &' && \
    sleep 5s && \
    mysqladmin -uroot password 'root'

# 复制mysql+nginx配置文件
COPY ./config/docker-php-ext-mysqli.ini /usr/local/etc/php/conf.d
COPY ./config/docker-php-ext-pdo_mysql.ini /usr/local/etc/php/conf.d
COPY ./config/nginx.conf /etc/nginx/nginx.conf

# 复制web项目源码
COPY hellocms /var/www/html

# 重新设置源码路径的用户所有权
RUN chown -R www-data:www-data /var/www/html

# 创建低权限用户并设置弱密码
RUN useradd -ms /bin/bash helloctf && \
    echo "helloctf:123456" | chpasswd

# 设置SSH配置以允许密码登录
RUN sed -i 's/#PasswordAuthentication yes/PasswordAuthentication yes/' /etc/ssh/sshd_config && \
    echo "PermitRootLogin yes" >> /etc/ssh/sshd_config && \
    mkdir /var/run/sshd

# 设置关卡信息
COPY level-info.txt /etc/level-info.txt
RUN rm -rf /etc/update-motd.d/* && \
    echo "" > /etc/motd && \
    echo "" >  /etc/issue.net && \
    echo "" >  /etc/legal && \
    echo '#!/bin/sh\nif [ -f /etc/level-info.txt ]; then\n    cat /etc/level-info.txt\nfi' > /etc/update-motd.d/99-custom-message && \
    chmod +x /etc/update-motd.d/99-custom-message


# 拷贝容器入口点脚本
COPY ./service/docker-entrypoint.sh /docker-entrypoint.sh
COPY ./service/check.sh /usr/local/bin/check.sh
RUN chmod +x /docker-entrypoint.sh /usr/local/bin/check.sh

# 设置shell的工作目录
WORKDIR /var/www/html

# 配置数据库数据
RUN sh -c 'mysqld_safe &' \
	&& sleep 5s \
    && mysql -e "source /var/www/html/install/hellocms.sql;" -uroot -proot

# 暴露SSH端口
EXPOSE 22

# 设置nginx日志保存目录
VOLUME ["/var/log/nginx"]

# 设置容器入口点
ENTRYPOINT [ "/docker-entrypoint.sh" ]
