# Development only
FROM keviocastro/laravel:5

RUN apt-get update && apt-get install -y \
 	mysql-client \
    vim \
 	nmap \
    git \
    --no-install-recommends && rm -r /var/lib/apt/lists/*

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('SHA384', 'composer-setup.php') === 'aa96f26c2b67226a324c27919f1eb05f21c248b987e6195cad9690d5c1ff713d53020a02ac8c217dbf90a7eacc9d141d') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php --install-dir=/usr/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"

# Install Xdebug
RUN curl -fsSL 'https://xdebug.org/files/xdebug-2.4.0.tgz' -o xdebug.tar.gz \
    && mkdir -p xdebug \
    && tar -xf xdebug.tar.gz -C xdebug --strip-components=1 \
    && rm xdebug.tar.gz \
    && ( \
    cd xdebug \
    && phpize \
    && ./configure --enable-xdebug \
    && make -j$(nproc) \
    && make install \
    ) \
    && rm -r xdebug

RUN echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.default_enable=1"               >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=1"                >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_mode=req"                >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_handler=dbgp"            >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=1"             >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_connect_back=1"          >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_port=9000"               >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_host=192.168.0.19"       >> /usr/local/etc/php/conf.d/xdebug.ini \
    # && echo "xdebug.profiler_enable=0"              >> /usr/local/etc/php/conf.d/xdebug.ini \
    # && echo "xdebug.profiler_enable_trigger=1"      >> /usr/local/etc/php/conf.d/xdebug.ini \
    # && echo "xdebug.idekey=sublime.xdebug"          >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_log=/var/log/xdebug_remote.log" >> /usr/local/etc/php/conf.d/xdebug.ini


COPY docker/entrypoint.sh /usr/local/bin/docker-dev-entrypoint
RUN chmod +x /usr/local/bin/docker-dev-entrypoint

# Entrypoint resets command
ENTRYPOINT ["docker-dev-entrypoint"]
CMD ["docker-entrypoint"]