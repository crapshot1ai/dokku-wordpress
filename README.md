# deploy wordpress blog with dokku ... 

based on https://kapn.net/support/set-up-your-wordpress-site-on-dokku/ added composer.json / composer.lock to install
extension gd, mbstring, exif, imagick

All files needed for deployment (except wordpress files) are contained in this repository. Instead of generating the 
files as decribed here all files can also just be copied to the directory from which the blog is to be deployed


example: deploy wordpress blog for **example.com** with the app name **blog** yielding **blog.example.com**

### create folder for deployment

`mkdir blog`  
`cd blog`

### create app named **blog**
`dokku apps:create blog`  

### install mariadb plugin for dokku
`sudo dokku plugin:install https://github.com/dokku/dokku-mariadb.git mariadb`  

### create database named **blogdb** # -i/-I sets mariadb version`
`dokku mariadb:create blogdb -i mariadb -I 10.4.17` # generates database blogdb  

### link database **blogdb** to app **blog***  
`dokku mariadb:link blogdb blog` # links blogdb to blog and writes ENV variable 'DATABASE_URL' containing database infos for login  

### create persistent storage to save themes, plugins, uploads (storing everythin in one dir; works) (ToDo: do with three separate directories for clarity)  

`sudo mkdir -p /var/lib/dokku/data/storage/blog/wp-content/` # generate directors\
`sudo chown 32767:32767 /var/lib/dokku/data/storage/blog/wp-content/` #set permissions 32767:32767 \
`dokku storage:mount record /var/lib/dokku/data/storage/blog/wp-content:/app/wp-content` #mount to persistent storage 

### Download latest wordpress version

`curl -LO https://wordpress.org/latest.zip` \
`unzip latest.zip`   
`rm -rf latest.zip` 

`cd wordpress`  

### Download wordpress gitignore file to root folder that will be pushed (here wordpress)
`curl https://raw.githubusercontent.com/github/gitignore/master/WordPress.gitignore > .gitignore`

### Initialize repository and make initial commit 

`git init` \
`git add .` \
`git commit -m "initial commit of wordpress files"`


### rename wp-config-sample.php to wp-config and modify

**edit 1:** replace the corresponding section to import database info (host, user, password, database) from ENV variable "DATABASE_URL" set by mariadb


```
// ** MySQL settings - You can get this info from your web host ** the following line writes ENV variable 'DATABASE_URL' to $url //
$url = parse_url(getenv("DATABASE_URL"));

// ** the next lines extract host, uername, password, database from 'DATABASE_URL'
$host = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$database = substr($url["path"], 1);

/** The name of the database for WordPress */
define( 'DB_NAME', $database );

/** MySQL database username */
define( 'DB_USER', $username );

/** MySQL database password */
define( 'DB_PASSWORD', $password );

/** MySQL hostname */
define( 'DB_HOST', $host );
```

### create and insert keys and salts. Go to https://api.wordpress.org/secret-key/1.1/salt/ and copy output
#### ToDo: set keys and salts as ENV variable and import with getenv()

**edit 2:** replace the corresponding section with the output keys and salts

### Ensure working of wp under "https"

**edit 3:** copy the following section to the end of wp-config.php

```
/** Ensure ssl is detected and responded to appropriately */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
    $_SERVER['HTTPS'] = 'on';
}
```

### create custom_php.ini in root folder containing

**custom.php.ini**
```
upload_max_filesize = 128M
post_max_size = 128M
max_execution_time = 60
memory_limit = 512M
```
### create Procfile to define general buildpack and use of custom_php.ini 

**Procfile**
```
web: vendor/bin/heroku-php-apache2 -i custom_php.ini 
```

### define php buildpack and add to ENV variable
`dokku buildpacks:add blog https://github.com/heroku/heroku-buildpack-php`   
[use `dokku buildpacks:list blog` to list linked buildpacks for an app]

### create composer.json to define extensions; #gd: needed for wordpress; exif, mbstring, imagick are not needed but nice to have

**composer.json**
```
{
    "require": {
  "php": ">=5.6.0",
        "ext-gd": "*", 
        "ext-mbstring": "*",
        "ext-imagick": "*",
  "ext-exif": "*"
    }
}
```

### create composer.lock file containing:

**composer.lock**
```
{
    "_readme": [
        "This file locks the dependencies of your project to a known state",
        "Read more about it at https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies",
        "This file is @generated automatically"
    ],
    "content-hash": "072c20860c32c097cf6dc96625ac8536",
    "packages": [],
    "packages-dev": [],
    "aliases": [],
    "minimum-stability": "stable",
    "stability-flags": [],
    "prefer-stable": false,
    "prefer-lowest": false,
    "platform": {
        "php": ">=5.6.0",
        "ext-gd": "*",
        "ext-mbstring": "*",
        "ext-imagick": "*",
        "ext-exif": "*"
    },
    "platform-dev": [],
    "plugin-api-version": "2.1.0"
}
```

### composer lock can also be generated by running composer install in deployment directory containing composer.json only (installed php and installed composer needed)

### Commit and push

`git add .` \
`git commit -m 'update config'` \
`git remote add dokku dokku@hostname.tld:blog` #hostname = your servers hostname, where you host **example.com**   
`git push dokku master`

### to copy remote files to local /*/storage

`sudo rsync -avz wp-content/ /var/lib/dokku/data/storage/blog/wp-content/`\
`sudo chown -R 32767:32767 /var/lib/dokku/data/storage/blog/wp-content/`

### install and enable letsencrypt plugin to get ssl certificate

`sudo dokku plugin:install https://github.com/dokku/dokku-letsencrypt.git` #install plugin \
`dokku config:set --global DOKKU_LETSENCRYPT_EMAIL=your@email.tld` #define email ... only once \
`dokku letsencrypt:cron-job --add` #setup cronjob to do auto renewal \
`dokku letsencrypt:enable blog`

## website to be reached via **blog.example.com** served via https