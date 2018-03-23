FROM postgres:10.1

ENV PG_CRON_VERSION    "1.0.2"
ENV TZ=Europe/Moscow
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update && apt-get install -y \
    postgresql-server-dev-all postgresql-contrib \
    #libcurl4-nss-dev libcurl4-gnutls-dev \
    libcurl4-openssl-dev \
    wget jq cmake build-essential

# Install PG_CRON:
RUN mkdir /build && \
    cd /build && \
    wget https://github.com/citusdata/pg_cron/archive/v$PG_CRON_VERSION.tar.gz && \
    tar xzvf v$PG_CRON_VERSION.tar.gz && \
    cd pg_cron-$PG_CRON_VERSION && \
    make && \
    make install
