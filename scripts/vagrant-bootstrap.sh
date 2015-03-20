# php modules
sudo apt-get update
sudo apt-get -y install php-pear sqlite3 php5-sqlite
sudo pecl install mailparse
sudo sh -c 'echo "extension=mailparse.so" >> /etc/php5/apache2/php.ini'
sudo sh -c 'echo "extension=mailparse.so" >> /etc/php5/cli/php.ini'

wget https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit

sudo service apache2 restart
