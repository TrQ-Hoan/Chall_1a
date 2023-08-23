# Chall-1a

## setup

- Install

```
sudo apt install apache2 mysql-server php libapache2-mod-php php-mysql
```

- Create source folder

```
sudo mkdir -m 777 /var/www/html/chall01
sudo vim /etc/apache2/sites-available/chall01.conf
sudo a2ensite chall01.conf
systemctl reload apache2
```

file config [/etc/apache2/sites-available/chall01.conf](./config/chall01.conf)

- First setup mysql

```
vim /etc/mysql/mysql.conf.d/mysqld.cnf
# bind-address            = 0.0.0.0
# mysqlx-bind-address     = 0.0.0.0
sudo service mysql restart

sudo mysql -u root

CREATE DATABASE chall01;
CREATE USER 'admin'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON chall01.* TO 'admin'@'localhost';
CREATE USER 'remote<ip>'@'<ip>' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON chall01.* TO 'remote<ip>'@'<ip>';
FLUSH PRIVILEGES;
EXIT;

sudo mysql -u admin -p chall01
```