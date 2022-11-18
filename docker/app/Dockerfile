FROM php:8.1.12-fpm-bullseye as base

###
# <Copy from nginx image>
###
LABEL maintainer="NGINX Docker Maintainers <docker-maint@nginx.com>"
ENV NGINX_VERSION   1.22.1
ENV NJS_VERSION     0.7.7
ENV PKG_RELEASE     1~bullseye
RUN set -x \
# create nginx user/group first, to be consistent throughout docker variants
    && addgroup --system --gid 101 nginx \
    && adduser --system --disabled-login --ingroup nginx --no-create-home --home /nonexistent --gecos "nginx user" --shell /bin/false --uid 101 nginx \
    && apt-get update \
    && apt-get install --no-install-recommends --no-install-suggests -y gnupg1 ca-certificates \
    && \
    NGINX_GPGKEY=573BFD6B3D8FBC641079A6ABABF5BD827BD9BF62; \
    found=''; \
    for server in \
        hkp://keyserver.ubuntu.com:80 \
        pgp.mit.edu \
    ; do \
        echo "Fetching GPG key $NGINX_GPGKEY from $server"; \
        apt-key adv --keyserver "$server" --keyserver-options timeout=10 --recv-keys "$NGINX_GPGKEY" && found=yes && break; \
    done; \
    test -z "$found" && echo >&2 "error: failed to fetch GPG key $NGINX_GPGKEY" && exit 1; \
    apt-get remove --purge --auto-remove -y gnupg1 && rm -rf /var/lib/apt/lists/* \
    && dpkgArch="$(dpkg --print-architecture)" \
    && nginxPackages=" \
        nginx=${NGINX_VERSION}-${PKG_RELEASE} \
        nginx-module-xslt=${NGINX_VERSION}-${PKG_RELEASE} \
        nginx-module-geoip=${NGINX_VERSION}-${PKG_RELEASE} \
        nginx-module-image-filter=${NGINX_VERSION}-${PKG_RELEASE} \
        nginx-module-njs=${NGINX_VERSION}+${NJS_VERSION}-${PKG_RELEASE} \
    " \
    && case "$dpkgArch" in \
        amd64|arm64) \
# arches officialy built by upstream
            echo "deb https://nginx.org/packages/debian/ bullseye nginx" >> /etc/apt/sources.list.d/nginx.list \
            && apt-get update \
            ;; \
        *) \
# we're on an architecture upstream doesn't officially build for
# let's build binaries from the published source packages
            echo "deb-src https://nginx.org/packages/debian/ bullseye nginx" >> /etc/apt/sources.list.d/nginx.list \
            \
# new directory for storing sources and .deb files
            && tempDir="$(mktemp -d)" \
            && chmod 777 "$tempDir" \
# (777 to ensure APT's "_apt" user can access it too)
            \
# save list of currently-installed packages so build dependencies can be cleanly removed later
            && savedAptMark="$(apt-mark showmanual)" \
            \
# build .deb files from upstream's source packages (which are verified by apt-get)
            && apt-get update \
            && apt-get build-dep -y $nginxPackages \
            && ( \
                cd "$tempDir" \
                && DEB_BUILD_OPTIONS="nocheck parallel=$(nproc)" \
                    apt-get source --compile $nginxPackages \
            ) \
# we don't remove APT lists here because they get re-downloaded and removed later
            \
# reset apt-mark's "manual" list so that "purge --auto-remove" will remove all build dependencies
# (which is done after we install the built packages so we don't have to redownload any overlapping dependencies)
            && apt-mark showmanual | xargs apt-mark auto > /dev/null \
            && { [ -z "$savedAptMark" ] || apt-mark manual $savedAptMark; } \
            \
# create a temporary local APT repo to install from (so that dependency resolution can be handled by APT, as it should be)
            && ls -lAFh "$tempDir" \
            && ( cd "$tempDir" && dpkg-scanpackages . > Packages ) \
            && grep '^Package: ' "$tempDir/Packages" \
            && echo "deb [ trusted=yes ] file://$tempDir ./" > /etc/apt/sources.list.d/temp.list \
# work around the following APT issue by using "Acquire::GzipIndexes=false" (overriding "/etc/apt/apt.conf.d/docker-gzip-indexes")
#   Could not open file /var/lib/apt/lists/partial/_tmp_tmp.ODWljpQfkE_._Packages - open (13: Permission denied)
#   ...
#   E: Failed to fetch store:/var/lib/apt/lists/partial/_tmp_tmp.ODWljpQfkE_._Packages  Could not open file /var/lib/apt/lists/partial/_tmp_tmp.ODWljpQfkE_._Packages - open (13: Permission denied)
            && apt-get -o Acquire::GzipIndexes=false update \
            ;; \
    esac \
    \
    && apt-get install --no-install-recommends --no-install-suggests -y \
                        $nginxPackages \
                        gettext-base \
                        curl \
    && apt-get remove --purge --auto-remove -y && rm -rf /var/lib/apt/lists/* /etc/apt/sources.list.d/nginx.list \
    \
# if we have leftovers from building, let's purge them (including extra, unnecessary build deps)
    && if [ -n "$tempDir" ]; then \
        apt-get purge -y --auto-remove \
        && rm -rf "$tempDir" /etc/apt/sources.list.d/temp.list; \
    fi \
# forward request and error logs to docker log collector
    && ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log \
# create a docker-entrypoint.d directory
    && mkdir /docker-entrypoint.d
###
# </Copy from nginx image>
###

RUN set -ex \
    && apt-get update && apt-get install --no-install-recommends --no-install-suggests -y \
    # need for node
    gnupg2

###
# <Copy from node image>
###
ENV NODE_VERSION 18.12.1
RUN ARCH= && dpkgArch="$(dpkg --print-architecture)" \
  && case "${dpkgArch##*-}" in \
    amd64) ARCH='x64';; \
    ppc64el) ARCH='ppc64le';; \
    s390x) ARCH='s390x';; \
    arm64) ARCH='arm64';; \
    armhf) ARCH='armv7l';; \
    i386) ARCH='x86';; \
    *) echo "unsupported architecture"; exit 1 ;; \
  esac \
  # gpg keys listed at https://github.com/nodejs/node#release-keys
  && set -ex \
  && for key in \
    4ED778F539E3634C779C87C6D7062848A1AB005C \
    141F07595B7B3FFE74309A937405533BE57C7D57 \
    74F12602B6F1C4E913FAA37AD3A89613643B6201 \
    61FC681DFB92A079F1685E77973F295594EC4689 \
    8FCCA13FEF1D0C2E91008E09770F7A9A5AE15600 \
    C4F0DFFF4E8C1A8236409D08E73BC641CC11F4C8 \
    890C08DB8579162FEE0DF9DB8BEAB4DFCF555EF4 \
    C82FA3AE1CBEDC6BE46B9360C43CEC45C17AB93C \
    108F52B48DB57BB0CC439B2997B01419BD92F80A \
  ; do \
      gpg --batch --keyserver hkps://keys.openpgp.org --recv-keys "$key" || \
      gpg --batch --keyserver keyserver.ubuntu.com --recv-keys "$key" ; \
  done \
  && curl -fsSLO --compressed "https://nodejs.org/dist/v$NODE_VERSION/node-v$NODE_VERSION-linux-$ARCH.tar.xz" \
  && curl -fsSLO --compressed "https://nodejs.org/dist/v$NODE_VERSION/SHASUMS256.txt.asc" \
  && gpg --batch --decrypt --output SHASUMS256.txt SHASUMS256.txt.asc \
  && grep " node-v$NODE_VERSION-linux-$ARCH.tar.xz\$" SHASUMS256.txt | sha256sum -c - \
  && tar -xJf "node-v$NODE_VERSION-linux-$ARCH.tar.xz" -C /usr/local --strip-components=1 --no-same-owner \
  && rm "node-v$NODE_VERSION-linux-$ARCH.tar.xz" SHASUMS256.txt.asc SHASUMS256.txt \
  && ln -s /usr/local/bin/node /usr/local/bin/nodejs \
  # smoke tests
  && node --version \
  && npm --version
###
# </Copy from node image>
###

# install composer from official image @see https://hub.docker.com/_/composer
COPY --from=composer:2.4.4 /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install --no-install-recommends --no-install-suggests -y \
    supervisor \
    gettext-base \
    dos2unix \
    # for running some scripts as www-data inside container
    sudo \
    # install php extensions
    # using mlocati/docker-php-extension-installer recomended by official php docker repo
    # @see https://github.com/docker-library/docs/blob/master/php/README.md#php-core-extensions
    && curl -sSLf -o /usr/local/bin/install-php-extensions https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions \
    && install-php-extensions \
        pdo_mysql \
        mysqli \
        json \
        mbstring \
        zip \
        xml \
        bcmath

# supervisord config
COPY ./docker/app/supervisord.conf /etc/supervisord.conf

# add custom php-fpm pool settings, these get written at entrypoint startup
ENV FPM_PM_MAX_CHILDREN=20 \
    FPM_PM_START_SERVERS=2 \
    FPM_PM_MIN_SPARE_SERVERS=1 \
    FPM_PM_MAX_SPARE_SERVERS=3 \
    NGINX_READ_TIMEOUT=60

### php-fpm config ###
COPY ./docker/app/zzz-app.conf /tmp/zzz-app.conf
# name of the file is `zzz-app.conf`, so that the directive in /usr/local/etc/php-fpm.conf `include=etc/php-fpm.d/*.conf`
# would include zzz-app.conf file as the latest and according php-fpm configuration overwrite any previous config files
RUN envsubst '$FPM_PM_MAX_CHILDREN $FPM_PM_START_SERVERS $FPM_PM_MIN_SPARE_SERVERS $FPM_PM_MAX_SPARE_SERVERS' < /tmp/zzz-app.conf > /usr/local/etc/php-fpm.d/zzz-app.conf

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
# custom php.ini
COPY ./docker/app/php-prod.ini /tmp/zzz-php.ini

### nginx config ###
COPY ./docker/app/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/app/nginx-site.conf /tmp/nginx-site.conf
RUN mkdir -p /etc/nginx/conf.d \
    && envsubst '$NGINX_READ_TIMEOUT' < /tmp/nginx-site.conf > /etc/nginx/conf.d/default.conf

### dev stage ###
FROM base as dev

WORKDIR /var/www/html

# in development we can install all dependencies to project root (more convinient for development, for example, debugging)
# compose install / npm install could be done developer manually after running `docker-compose exec app bash`
ENV NODE_ENV="development"

EXPOSE 80

### copy entrypoint ###
COPY ./docker/app/docker-php-entrypoint-dev /usr/local/bin/
RUN dos2unix /usr/local/bin/docker-php-entrypoint-dev \
    && chmod 770 /usr/local/bin/docker-php-entrypoint-dev

### dependencies stage ###
# for running installing non-dev dependencies of composer and npm ###
FROM base as dependencies

WORKDIR /var/www/html

# copy possible seeds in storage dir separately for docker image cache
COPY --chown=www-data:www-data ./storage ./storage

##
## Compose Package Manager
##
COPY --chown=www-data:www-data ./composer.json ./composer.lock* ./
# RUN composer config github-oauth.github.com YOUROAUTHKEYHERE
# for some reason if you run on hosting with flag doctrine is not install
RUN composer install --no-scripts --no-autoloader --ansi --no-interaction

##
## Npm Install And Node Build Tools (vite)
##
ENV NODE_ENV="development"
COPY --chown=www-data:www-data ./package*.json ./
RUN npm config list \
    # is better then `npm install` for production
    && npm ci || npm install
ENV NODE_ENV="production"

COPY --chown=www-data:www-data ./vite.config.js ./
# didn't find to copy (without resources/view folder) in one COPY command without changing directory structure
# https://stackoverflow.com/a/37715522
COPY --chown=www-data:www-data ./resources/js ./resources/js
COPY --chown=www-data:www-data ./resources/css ./resources/css

##
## Copy Other Files
##
# didn't find a way to copy ./ ./ without ./storage, which was copied earlier @see https://github.com/moby/moby/issues/15771
COPY --chown=www-data:www-data ["./artisan", "./"]
COPY --chown=www-data:www-data ./bootstrap ./bootstrap
COPY --chown=www-data:www-data ./config ./config
COPY --chown=www-data:www-data ./database ./database
COPY --chown=www-data:www-data ./public ./public
COPY --chown=www-data:www-data ./resources/views ./resources/views
COPY --chown=www-data:www-data ./routes ./routes
COPY --chown=www-data:www-data ./lang ./lang
COPY --chown=www-data:www-data ./app ./app

##
## Run build of frontend scaffolding
##
RUN composer dump-autoload -o
RUN npm run build

##
## Delete Files That Are Not Used In Production
##
# node_modules / css / js and images are compiled to ./public
RUN rm -rfv /var/www/html/tests \
    && rm -rfv /var/www/html/node_modules \
    && rm -rfv /var/www/html/resources/css \
    && rm -rfv /var/www/html/resources/js \
    && rm -rfv /var/www/html/resources/images \
    && rm -rfv /var/www/html/resources/fonts

### copy entrypoint and overwrite default one of base php image ###
COPY ./docker/app/docker-php-entrypoint-prod /usr/local/bin/
RUN dos2unix /usr/local/bin/docker-php-entrypoint-prod \
    && chmod 770 /usr/local/bin/docker-php-entrypoint-prod

# 80 - for nginx
EXPOSE 80

ENTRYPOINT ["docker-php-entrypoint-prod"]

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]
