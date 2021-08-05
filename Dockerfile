# base
FROM npulidom/alpine-phalcon
LABEL maintainer="nicolas.pulido@crazycake.tech"

ARG JPEGOPTIM_ORIGIN=https://github.com/tjko/jpegoptim/archive/RELEASE.1.4.6.tar.gz
ARG MOZJPEG_ORIGIN=https://github.com/mozilla/mozjpeg/archive/v3.3.1.tar.gz

# install packages
RUN apk update && apk add --no-cache --repository=http://dl-cdn.alpinelinux.org/alpine/edge/testing \
	# imagemagick
	imagemagick \
	php7-imagick \
	# png tools
	pngquant \
	# jpg tools
	libjpeg-turbo-dev \
	libjpeg-turbo-utils \
	# build tools
	gcc \
	make \
	autoconf \
	automake \
	libtool \
	pkgconf \
	musl-dev \
	nasm \
	tar \
	zlib \
	# mozjpeg
	&& \
	echo -e "building mozjpeg..." && \
	cd ~ && \
	mkdir -p /usr/src/mozjpeg && \
	curl -L ${MOZJPEG_ORIGIN} | tar xz -C /usr/src/mozjpeg --strip-components=1 && \
	cd /usr/src/mozjpeg && \
	autoreconf -fiv && \
	sh configure && \
	make install prefix=/usr/local libdir=/usr/local/lib64 && \
	cd ~ && \
	rm -rf /usr/src/mozjpeg && cd ~ && \
	# jpegoptim
	echo -e "building jpegoptim..." && \
	mkdir -p /usr/src/jpegoptim && \
	curl -L ${JPEGOPTIM_ORIGIN} | tar xz -C /usr/src/jpegoptim --strip-components=1 && \
	cd /usr/src/jpegoptim && \
	./configure && make && make strip && make install && \
	rm -rf /usr/src/jpegoptim && cd ~ && \
	# remove dev libs
	apk del \
	gcc \
	make \
	autoconf \
	automake \
	libtool \
	pkgconf \
	musl-dev \
	nasm \
	tar \
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
	chgrp -R www-data storage && \
	chmod -R 770 storage

# start app
CMD ["--nginx-env"]
