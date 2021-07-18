FROM php:7.4-cli


# Common
RUN apt-get update && apt-get install -y unzip git


# Composer
WORKDIR /app
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer


# Security tools
WORKDIR /opt

# testssl.sh
RUN apt-get install -y procps coreutils socat openssl xxd bsdmainutils dnsutils
RUN git clone --depth 1 https://github.com/drwetter/testssl.sh.git

# nmap
RUN apt-get install -y nmap

# Semgrep
RUN apt-get install -y python3-pip
RUN python3 -m pip install semgrep

# Docker
RUN curl -fsSL https://get.docker.com -o get-docker.sh
RUN sh get-docker.sh


# Setup
WORKDIR /app
COPY . /app
RUN composer install
RUN /app/scanner migrate --seed

# Entrypoint
ENTRYPOINT ["/app/scanner"]
