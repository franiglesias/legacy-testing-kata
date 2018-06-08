FROM ubuntu:xenial
MAINTAINER Mavi Jiménez <mavi@linkita.net>

RUN apt-get update \
    && apt-get install locales \
    && locale-gen en_US.UTF-8 \
    && LANG=en_US.UTF-8 apt-get install -y curl software-properties-common git wget vim \
    && LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php

# Install PHP with some extensions
RUN apt-get update && \
    apt-get --no-install-recommends --no-install-suggests --yes --quiet install \
        php7.2-cli php7.2-apcu php7.2-mbstring php7.2-curl php7.2-gd php7.2-imagick php7.2-intl php7.2-bcmath \
        php7.2-mysql php7.2-soap php7.2-xdebug php7.2-xml php7.2-zip php7.2-ldap && \
    apt-get clean && apt-get --yes --quiet autoremove --purge && \
    rm -rf  /var/lib/apt/lists/* /tmp/* /var/tmp/* \
            /usr/share/doc/* /usr/share/groff/* /usr/share/info/* /usr/share/linda/* \
            /usr/share/lintian/* /usr/share/locale/* /usr/share/man/*


# Install composer
RUN curl https://getcomposer.org/installer > composer-setup.php && \
                php composer-setup.php && \
                mv composer.phar /usr/local/bin/composer && \
                rm composer-setup.php

WORKDIR /var/www/dojo/


