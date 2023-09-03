# Chall-1a

## setup

- Install

```
sudo apt install apache2 mysql-server php libapache2-mod-php php-mysql
```

- Create source folder

```
cd ~
git clone https://github.com/TrQ-Hoan/Chall_1a.git
sudo chmod 775 ~ ~/Chall_1a
sudo chmod -R 775 ~/Chall_1a/source
sudo chown -R www-data:www-data ~/Chall_1a

mkdir -p ~/Chall_1a/source/archive/assignments ~/Chall_1a/source/archive/challenges ~/Chall_1a/source/archive/submits
sudo chmod -R 777 ~/Chall_1a/source/archive/
# sudo chmod -R 775 ~/Chall_1a/source/archive/
# sudo chown -R www-data:www-data ~/Chall_1a/source/archive/

# link source code directory to apache default directory
sudo ln -s ~/Chall_1a/source /var/www/html/chall01
# create new site file config
sudo vim /etc/apache2/sites-available/chall01.conf
# disable default conf
sudo a2dissite 000-default.conf
# enable new site config
sudo a2ensite chall01.conf
# restart service - update config
systemctl reload apache2
```

file config [/etc/apache2/sites-available/chall01.conf](./config/chall01.conf)
```
cp ~/Chall_1a/config/chall01.conf /etc/apache2/sites-available
```


- First setup mysql

```
# edit bind addr for usable on all interface
sudo vim /etc/mysql/mysql.conf.d/mysqld.cnf
# ```
# bind-address            = 0.0.0.0
# mysqlx-bind-address     = 0.0.0.0
# ```
sudo service mysql restart

sudo mysql -u root

# create database and user
CREATE DATABASE chall01;
CREATE USER 'admin'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON chall01.* TO 'admin'@'localhost';
CREATE USER 'remote<ip>'@'<ip>' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON chall01.* TO 'remote<ip>'@'<ip>';
FLUSH PRIVILEGES;
EXIT;

# test connect with new user on new database
mysql -u admin -p chall01
```
