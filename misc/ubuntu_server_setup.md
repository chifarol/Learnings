<!-- @format -->

# LEMP Setup and Laravel, NextJS and React

#### Articles

https://netshopisp.medium.com/how-to-install-nginx-mysql-php-on-ubuntu-22-04-lemp-33a7a134306a

## Init

1. update ubuntu

```
    sudo apt update
```

2. install nginx

```
    apt install nginx -y
```

3. start nginx

```
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
    systemctl enable --now php8.3-fpm
```

3. Install other modules

```
    apt-get install php8.3 php8.3-xml php8.3-mbstring php8.3-mysql php8.3-curl php8.3-mcrypt php8.3-gd php8.3-zip
```

## Setup Laravel

1. make Nginx to automatically start on boot.

```
    systemctl enable --now nginx
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

5. Perpare repo

```
    npm install
    npm run build
```

5. Setup nginx server block

```
    nano /etc/nginx/sites-available/default
```

```
server {
        listen 80 default_server;
        listen [::]:80 default_server;
        root /var/www/test/dist;
        # Add index.php to the list if you are using PHP
        index index.html index.htm index.nginx-debian.html;

        server_name _;

        location / {
                 # as directory, then fall back to displaying a 404.
                try_files $uri $uri/ =404;
        }
}
```

2. Link sites-available to sites enabled

```
    sudo ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
```

2. Link sites-available to sites enabled

```
    nginx -t
    systemctl restart nginx
```
