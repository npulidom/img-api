# base
FROM npulidom/alpine-phalcon
LABEL maintainer="nicolas.pulido@crazycake.tech"

ARG JPEGOPTIM_ORIGIN=https://github.com/tjko/jpegoptim/archive/RELEASE.1.4.6.tar.gz

# install packages
RUN apk update && apk add --no-cache --repository=http://dl-cdn.alpinelinux.org/alpine/edge/testing \
	# imagemagick
	imagemagick \
	php-imagick \
	# png tools
	pngquant \
	# jpg tools
	libjpeg-turbo-dev \
	libjpeg-turbo-utils \
	# build tools
	make \
	g++ \
	tar \
	# jpegoptim
	&& \
	mkdir -p /usr/src/jpegoptim && \
	curl -L ${JPEGOPTIM_ORIGIN} | tar xz -C /usr/src/jpegoptim --strip-components=1 && \
	cd /usr/src/jpegoptim && \
	./configure && make && make strip && make install && \
	rm -rf /usr/src/jpegoptim && cd / \
	&& \
	# remove dev libs
	apk del \
	make \
	g++ \
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
