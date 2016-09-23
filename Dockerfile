FROM keviocastro/
COPY . /var/www/html/

RUN cp /var/www/html/.env.example /var/www/html/.env 
RUN cd /var/www/html/ && php artisan key:generate

EXPOSE 80

ENTRYPOINT ["php", "/var/www/html/artisan", "serve", "--host=0.0.0.0", "--port=80"]