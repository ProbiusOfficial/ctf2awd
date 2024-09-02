FROM ubuntu:22.04


# apt更换镜像源，并安装相关依赖
RUN sed -i 's@//.*archive.ubuntu.com@//mirrors.ustc.edu.cn@g' /etc/apt/sources.list && \
    sed -i 's@//.*security.ubuntu.com@//mirrors.ustc.edu.cn@g' /etc/apt/sources.list

# Install necessary packages
RUN apt-get update && \
    apt-get install -y openssh-server sudo net-tools iproute2 ufw

# Create a low-privilege user with a weak password
RUN useradd -ms /bin/bash helloctf && \
    echo "helloctf:123456" | chpasswd && \
    usermod -aG sudo helloctf

# Set up SSH
RUN mkdir /var/run/sshd



# 初始化关卡信息

COPY level-info /etc/level-info

RUN rm -rf /etc/update-motd.d/* && \
    echo "" > /etc/motd && \
    echo "" >  /etc/issue.net && \
    echo "" >  /etc/legal && \
    echo '#!/bin/sh\nif [ -f /etc/level-info ]; then\n    cat /etc/level-info\nfi' > /etc/update-motd.d/99-custom-message && \
    chmod +x /etc/update-motd.d/99-custom-message

# Copy the entrypoint script and check script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
COPY check.sh /usr/local/bin/check.sh

# Ensure the entrypoint script and check script are executable
RUN chmod +x /usr/local/bin/docker-entrypoint.sh /usr/local/bin/check.sh

# Expose SSH port
# EXPOSE 22

# Set the entrypoint script
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
