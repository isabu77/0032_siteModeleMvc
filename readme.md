# SITE MODELE MVC


## Environnement technique

Modèle MVC PHP avec un dossier 'Core' contenant les classes génériques réutilisables en MVC  

et un dossier 'src' contenant les classes controller : Users, UserInfos et Site

- Les variables d'environnement, à adapter selon le serveur (Windows / Unix / serveur distant IIS), 
sont définies dans le fichier :
* www/src/config.php : si serveur distant IIS
* www/src/configUnix.php : si serveur localhost unix et Docker
* www/src/configWamp.php : si serveur localhost WAMP

ce fichier doit être créé sur le modèle de www/src/config.sample.php :
    $env = [
    'ENV_DEV' => true,
    'TABLE_PREFIX' => 'modele_',
    'MYSQL_HOSTNAME' => 'modele.mysql',
    'MYSQL_ROOT_PASSWORD' => 'modele',
    'MYSQL_DATABASE' => 'modele',
    'MYSQL_USER' => 'usermodele',
    'MYSQL_PASSWORD' => 'modelepwd',
    'ENV_TVA' => 1.2,
    'GMAIL_USER' => '',
    'GMAIL_PWD' => '',
    'GMAIL_PSEUDO' => '',
    'GMAIL_PASSWORD' => '',
    'GMAIL_PSEUDO' => '',
    'GOOGLE_CLIENTID' => "",
    'GOOGLE_SECRET' => ""
    ];

- le fichier .env est utilisé dans un environnement Docker sur Linux, 
    il est remplacé par config.php pour la portabilité de certaines variables
    seules les variables suivantes sont utilisées par docker dans docker-compose.yml (voir .env.sample) :
        CONTAINER_NAME=modele
        CONTAINER_MYSQL=modele.mysql
        CONTAINER_PORT=8086
        SQL_CLIENT_PORT=3306
        SQL_INTERNAL_PORT=3306
        PORT_MAIL=1081

        ENV_DEV=true

        MYSQL_ROOT_PASSWORD=modele
        MYSQL_DATABASE=modele
        MYSQL_USER=usermodele
        MYSQL_PASSWORD=modelepwd
        TABLE_PREFIX=modele_

        GMAIL_MAIL=
        GMAIL_PASSWORD=
        GMAIL_PSEUDO=

- sur Windows/Wamp : 
    - copier le dossier **www** dans un dossier 'siteModeleMvc' de c:\Wamp64\www\
    - créer le fichier configWamp.php sur le modèle de config.sample.php
    - déclarer ce dossier dans le fichier hosts : 127.0.0.1 siteModeleMvc
    - ajouter dans httpd-vhosts.conf un bloc  <VirtualHost:80> les lignes suivantes :
       
        ServerName siteModeleMvc
        ServerAlias siteModeleMvc
        DocumentRoot "${INSTALL_DIR}/www/siteModeleMvc/public
        
    - installation du projet dans la console : 
        - cd c:\wamp64\www\siteModeleMvc
        - composer update
        - php commande\createsql.php --demo