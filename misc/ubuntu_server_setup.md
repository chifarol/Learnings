<!-- @format -->

# LEMP Setup and Laravel, NextJS and React

#### Articles

https://netshopisp.medium.com/how-to-install-nginx-mysql-php-on-ubuntu-22-04-lemp-33a7a134306a

## Init

1. update ubuntu

```
    sudo apt update
    sudo apt upgrade
```

2. install nginx

```
    apt install nginx -y
```

3. start nginx

```
    sudo apachectl stop
    systemctl start nginx
```

4. make Nginx to automatically start on boot.

```
    systemctl enable --now nginx
```

5. Install NGINX and Certbot.

```
    sudo apt install nginx certbot python3-certbot-nginx
```

6. #Allow Firewall Access.

```
    sudo ufw allow "Nginx Full"
    ufw allow OpenSSH
    ufw enable
```

## Install MariaDB

1. Install mariaDB

```
    apt install mariadb-server -y
```

2. Start MariaDB and enable autostart.

```
    systemctl status mariadb
```

### Secure MariaDB Installation

1. Start by running the script.

```
    mysql_secure_installation
```

- current password: (enter nothing and press ENTER)
- Switch to unix_socket authentication [Y/n] y
- Change the root password? [Y/n] n
- Remove anonymous users? [Y/n] y
- Disallow root login remotely? [Y/n] y
- Remove test database and access to it? [Y/n] y
- Reload privilege tables now? [Y/n] y

## Install PHP

1. install latest php.

```
    apt install php-fpm
```

2. install php fpm.

```
    systemctl enable --now php8.2-fpm
```

3. Install other modules

```
    apt-get install php8.2 php8.2-xml php8.2-mbstring php8.2-mysql php8.2-curl php8.2-mcrypt php8.2-gd php8.2-zip php7.2-cli php8.2-opcache php8.2-gd

```

## Setup MySQL & Adminer

1. Download adminer script at https://www.adminer.org/ apt-get install adminer
2. Upload it to /var/www/db, you can rename it to index.php (mv adminer.php db/index.php)
3. Configure nginx server block

```
nano /etc/nginx/sites-available/db
```

```
server {
   server_name db.mobirevo.com;  # Replace with your domain or IP address
      root /usr/share/adminer;  # Adminer's root directory
      index adminer.php;

      access_log     /var/log/nginx/admin.access.log;
      error_log      /var/log/nginx/admin.error.log;

      location / {
        try_files $uri $uri/ =404;
      }

      location ~ \.php$ {
          include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
      }

      location /adminer {
        alias /usr/share/adminer;
      }
}

```

4. Create symbolic link

```
sudo ln -s /etc/nginx/sites-available/db /etc/nginx/sites-enabled/db
```

4. Create User and assign password

```
CREATE USER 'puser'@'localhost' IDENTIFIED BY 'M83Re930?#yx';
GRANT ALL PRIVILEGES ON *.* TO 'puser'@localhost IDENTIFIED BY 'M83Re930?#yx';
FLUSH PRIVILEGES;
```

5. allow remote access to mysql from specific IP

```
// edit config file
sudo nano /etc/mysql/mariadb.conf.d/50-server.cnf

// find and change the following line
bind-address = 127.0.0.1

 // to
# bind-address = 127.0.0.1

sudo systemctl restart mariadb
```

```
GRANT ALL ON * to 'admin'@'192.168.50.254' IDENTIFIED BY 'li82tE0Tr3@dE' WITH GRANT OPTION;

OR

GRANT ALL PRIVILEGES ON *.* to 'chifarol'@'%' IDENTIFIED BY 'li82tE0Tr3@dE' WITH GRANT OPTION;

FLUSH PRIVILEGES;
```

```
sudo ufw allow from 102.90.101.156 to 3306
OR
sudo ufw allow 3306
sudo ufw reload
```

## Setup Laravel

1. install composer.

```
apt install composer
```

2. Perpare repo

```
    composer install
    npm install
    npm run build
```

3. Populate .env file
4. give the web server user write access to the storage and cache folders, where Laravel stores application-generated files:

```
sudo chown -R www-data:www-data /var/www/api/storage
sudo chown -R www-data:www-data /var/www/api/bootstrap/cache
```

5. Configure nginx server

```
sudo nano /etc/nginx/sites-available/api
sudo ln -s /etc/nginx/sites-available/api /etc/nginx/sites-enabled/api
```

```
    server {
        server_name domain.com www.domain.com;
        root /var/www/**your_folder**/public;

        add_header X-Frame-Options "SAMEORIGIN";
        add_header X-XSS-Protection "1; mode=block";
        add_header X-Content-Type-Options "nosniff";

        index index.html index.htm index.php;

        charset utf-8;

        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }

        location = /favicon.ico { access_log off; log_not_found off; }
        location = /robots.txt  { access_log off; log_not_found off; }

        error_page 404 /index.php;


        location ~ \.php$ {
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/var/run/php/php-fpm.sock;
        }
    }
```

```
    systemctl enable --now nginx
```

3. Configure supervisor to run background queues

```
    sudo apt-get install supervisor
    sudo nano /etc/supervisor/conf.d/laravel-worker.conf

    [program:laravel-worker]
    process_name=%(program_name)s_%(process_num)02d
    command=php /var/www/api/artisan queue:work
    autostart=true
    autorestart=true
    user=root
    numprocs=1
    redirect_stderr=true
    stdout_logfile=/var/log/laravel-worker.log

    sudo supervisorctl reread
    sudo supervisorctl update
    sudo supervisorctl start "laravel-worker:*"
```

## Setup Node Apps

1. make Nginx to automatically start on boot.

```
    apt install npm
```

2. install nodejs (with nvm).

```
    curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.1/install.sh | bash
    exec $SHELL
    nvm install --lts
    nvm install 20 #or version of choice
    nvm use 20
    nvm alias default 20
```

3. go to /var/www and create folder e,g "admin" and cd into it.

```
    cd /var/www
    mkdir admin
    cd admin
```

4. Setup git.

```
    git init
    git remote add origin https://--github_token--@--repo-path-url
    git pull origin master
```

### Deploy react app

1. Perpare repo

```
    npm install
    npm run build
```

2. Setup nginx server block

```
    nano /etc/nginx/sites-available/default
```

```
server {

        root /var/www/usemonnee.com/quickrpay-landing-page/dist;
        # Add index.php to the list if you are using PHP
        index index.html index.htm index.nginx-debian.html;

        server_name usemonnee.com;

        location / {
                 # as directory, then fall back to displaying a 404.
                try_files $uri $uri/  /index.html;
        }
}
```

3. Link sites-available to sites enabled

```
    sudo ln -s etc/nginx/sites-available/usemonnee.com /etc/nginx/sites-enabled/usemonnee.com
```

4. Restart server

```
    nginx -t
    systemctl restart nginx
```

### Deploy NEXTJS app

1. Perpare repo

```
    npm install
    npm run build
```

2. Setup nginx server block

```
    nano /etc/nginx/sites-available/default
```

```
server {
        listen 80 default_server;
        listen [::]:80 default_server;

        server_name _;

        gzip on;
        gzip_proxied any;
        gzip_types  application/javascript application/x-javascript text/css text/javascript;
        gzip_comp_level 5;
        gzip_buffers 16 8k;
        gzip_min_length 256;

         location /_next/static/ {
                alias /var/www/*your_folder*/.next/static/;
                expires 365d;
                access_log off;
        }

        location / {
                proxy_pass http://127.0.0.1:3000; #change ports for second app i.e. 3001,3002
                proxy_http_version 1.1;
                proxy_set_header Upgrade $http_upgrade;
                proxy_set_header Connection 'upgrade';
                proxy_set_header Host $host;
                proxy_cache_bypass $http_upgrade;
        }
}
```

3. Link sites-available to sites enabled

```
    sudo ln -s /etc/nginx/sites-available/app.mobirevo.com /etc/nginx/sites-enabled/app.mobirevo.com
```

4. Restart server

```
    npm install pm2 -g
    pm2 start npm --name mobirevo -- start
    nginx -t
    systemctl restart nginx
```

## Install NGINX and Certbot

```
sudo apt install nginx certbot python3-certbot-nginx
```

```
sudo certbot --nginx -d domainname.com -d www.domainname.com
```

## Setup Wordpress

https://www.digitalocean.com/community/tutorials/install-wordpress-nginx-ubuntu

## Block Bots

in robots.txt at root directory e.g /var/www/html

```
User-agent: *
Disallow: /
```

in Nginx server block

```
    set $block_bot 0;

    if ($http_user_agent ~* (googlebot|bingbot|slurp|baiduspider|yandex|sogou|exabot|facebookexternalhit|facebot|ia_archiver)) {
        set $block_bot 1;
    }

    if ($block_bot = 1) {
        return 403;
    }
```
