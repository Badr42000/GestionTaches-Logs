FROM debian:bookworm-slim

ENV DEBIAN_FRONTEND=noninteractive

RUN echo 'rsyslog-mysql rsyslog-mysql/dbconfig-install boolean false' | debconf-set-selections && \
    apt-get update && apt-get install -y \
    rsyslog \
    rsyslog-mysql \
    && rm -rf /var/lib/apt/lists/*

COPY docker/rsyslog/rsyslog.conf /etc/rsyslog.conf

EXPOSE 514/udp

CMD ["rsyslogd", "-n"]
