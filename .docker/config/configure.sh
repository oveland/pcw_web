#!/bin/sh
# Configure PHP
less $(echo "/etc/php/$PHP_VERSION/fpm/php.ini.template") | envsubst '\$PHP_TIMEZONE' > $(echo "/etc/php/$PHP_VERSION/fpm/php.ini")
less $(echo "/etc/php/$PHP_VERSION/cli/php.ini.template") | envsubst '\$PHP_TIMEZONE' > $(echo "/etc/php/$PHP_VERSION/cli/php.ini")

# Create php-fpm command
printf '#!/bin/sh \n bash service php$PHP_VERSION-fpm start \n echo "    **** $PHP_VERSION-fpm runnning!" \n' > /usr/bin/php-fpm && chmod +x /usr/bin/php-fpm

# Configure nginx
less /etc/nginx/conf.d/server.template | envsubst '\$NG_HOST \$NG_PORT \$NG_ROOT \$PHP_VERSION' > /etc/nginx/conf.d/server.conf

bash service nginx start;
echo "\n ****** Nginx server start on port: '$NG_PORT' | Exposed over external port: $NG_EXTERNAL_PORT"

bash php-fpm;


#write out current crontab
crontab -l > tmpcron
#echo new cron into cron file
echo "* * * * * /usr/bin/php /var/www/$NG_ROOT/artisan schedule:run >> /dev/null 2>&1" >> tmpcron
#install new cron file
crontab tmpcron
rm tmpcron

bash service cron start
echo "Cron configured successfully"

