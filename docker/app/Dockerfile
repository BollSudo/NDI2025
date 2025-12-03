FROM httpd:2.4-alpine

WORKDIR /usr/local/apache2/htdocs/

RUN apk add --no-cache curl \
 && rm -rf /var/cache/apk/*
RUN echo "ServerName ndi-prod" >> /usr/local/apache2/conf/httpd.conf
RUN rm -rf /usr/local/apache2/htdocs/*

COPY ./dist/ /usr/local/apache2/htdocs/

CMD ["httpd-foreground"]