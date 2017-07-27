# base
FROM npulidom/alpine-phalcon
LABEL maintainer="nicolas.pulido@crazycake.cl"

ARG JPEGOPTIM_ORIGIN=https://github.com/tjko/jpegoptim/archive/RELEASE.1.4.4.tar.gz

# install imagemagick, jpegoptim
RUN apk update && apk add -U --no-cache --repository=http://dl-cdn.alpinelinux.org/alpine/edge/testing \
	imagemagick-dev \
	libjpeg-turbo-dev \
	libjpeg-turbo-utils \
	make \
	g++ \
	autoconf \
	libtool \
	re2c \
	php7-dev \
	php7-pear \
	tar \
	wget \
	# jpegoptim
	&& mkdir -p /usr/src/jpegoptim && \
	wget -O - ${JPEGOPTIM_ORIGIN} | tar xz -C /usr/src/jpegoptim --strip-components=1 && \
	cd /usr/src/jpegoptim && \
	./configure && make && make strip && make install && \
	rm -rf /usr/src/jpegoptim && cd / && \
	#fix pecl && installs php7-imagemagick
	sed -i "$ s|\-n||g" /usr/bin/pecl && \
	pecl install -o -f imagick && \
	rm -rf /tmp/pear && \
	echo "extension=imagick.so" | tee -a /etc/php7/conf.d/02_imagick.ini && \
	# remove dev libs
	apk del \
	make \
	g++ \
	autoconf \
	libtool \
	gettext \
	pkgconf \
	php7-dev \
	php7-pear \
	php7-dom \
	php7-gettext \
	php7-pdo_mysql \
	php7-zip \
	tar \
	wget \
	&& rm -rf /var/cache/apk/*

# go to server dir
WORKDIR /var/www

# composer install dependencies
COPY composer.json .
RUN composer install --no-dev && composer dump-autoload --optimize --no-dev

# project code
COPY . .

# create app folder
RUN mkdir -p storage/cache storage/logs storage/uploads && \
	# set owner/perms
	chgrp -R www-data storage && chmod -R 770 storage && \
	# create symlink to public/uploads
	ln -sf /var/www/storage/uploads /var/www/public/uploads

# start app
CMD ["--nginx-env"]
