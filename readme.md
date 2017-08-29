# Todo FlightPHP

## Installation

### Install composer packages.

```lua
composer install
```

### Copy and edit Nginx config file.
```lua
cp flight.conf /etc/nginx/vhosts.d/flight.conf
mcedit /etc/nginx/vhosts.d/flight.conf
```

### Make a virtual host.
```lua
echo "127.0.0.1 flight.loc" >> /etc/hosts
nscd -i hosts
```

### Edit a database config file.
```lua
mcedit database_config.php
```

### Import a database dump file.

```lua
mysql DATABASE_NAME -uDATABASE_LOGIN -pDATABASE_PASSWORD < dump.sql
```

### Restart services.
```lua
service nginx restart;
service php-fpm restart;
```

### Install npm packages.

```lua
npm install
```






