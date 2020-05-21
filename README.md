# Heel en schoon

Heel en Schoon is een project van de Gemeente Amsterdam. Meer informatie over dit project is te vinden op de website van het [Datalab van de Gemeente Amsterdam](https://www.amsterdam.nl/bestuur-organisatie/organisatie/overige/datalab-amsterdam/)

Meer informatie [info@tiltshiftapps.nl](info@tiltshiftapps.nl)


## Waarom is deze code gedeeld

Het FIXXX-team van de Gemeente Amsterdam ontwikkelt software voor de gemeente.
Veel van deze software wordt vervolgens als open source gepubliceerd zodat andere
gemeentes, organisaties en burgers de software als basis en inspiratie kunnen 
gebruiken om zelf vergelijkbare software te ontwikkelen.
De Gemeente Amsterdam vindt het belangrijk dat software die met publiek geld wordt
ontwikkeld ook publiek beschikbaar is.

## Onderhoud en security

Deze repository bevat een "as-is" kopie van het project op moment van publiceren.
Deze kopie wordt niet actief onderhouden.

## Wat mag ik met deze code

De Gemeente Amsterdam heeft deze code gepubliceerd onder de Mozilla Public License v2.
Een kopie van de volledige licentie tekst is opgenomen in het bestand LICENSE.

Het FIXXX-team heeft de verdere doorontwikkeling van deze software overgedragen 
aan de probleemeigenaar. De code in deze repository zal dan ook niet actief worden
bijgehouden door het FIXXX-team.

## Open Source

Dit project maakt gebruik van diverse andere Open Source software componenten. O.a. 
[Symfony](http://www.symfony.com), 
[Doctrine](http://www.doctrine-project.org/), 
[Composer](https://getcomposer.org/), 
[Monolog](https://github.com/Seldaek/monolog), 
[Twig](http://twig.sensiolabs.org/), 
[Swiftmailer](http://swiftmailer.org/), 
[LiipImagine](https://github.com/liip/LiipImagineBundle),  
[NelmioAPI Doc](https://github.com/nelmio/NelmioApiDocBundle), 
[JSOR/Doctrine PostGIS](https://github.com/jsor/doctrine-postgis),
[Leaflet](https://github.com/Leaflet/Leaflet)

## Installeren

Om deze software te draaien moet je beschikking hebben over een webserver met PHP
(Apache, Nginx of IIS) en een PostgreSQL databaseserver met PostGIS.
Om afbeeldingen te beschermen wordt gebruik gemaakt van de secure link module van 
Nginx, er is een voorbeeld gegeven voor de configuratie hiervan. Voor andere 
webservers is een andere configuratie vereist en is het mogelijk noodzakelijk om 
de code aan te passen.

Maak een nieuwe PostgreSQL database aan voor dit project.

    CREATE DATABASE xx;
    CREATE USER yy WITH PASSWORD 'zz';
    GRANT ALL PRIVILEGES ON DATABASE xx TO yy;

Voer met een superuser onderstaand statement uit om de UUID en PostGIS functies 
beschikbaar te maken.

    CREATE EXTENSION IF NOT EXISTS "uuid-ossp"; 
    CREATE EXTENSION postgis;
    CREATE EXTENSION postgis_topology;

Clone de codebase

    git clone git@github.com:amsterdam/heelenschoon.git
    cd heelenschoon

Maak een aantal mappen aan om data op te kunnen slaan

    mkdir var
    mkdir var/data
    mkdir web/media

Afhankelijk van je systeem en de rechtenstructuur moet je sommige directories 
beschrijfbaar maken. Zie ook [Setting up Permissions in de handleiding van Symfony 3.0](http://symfony.com/doc/3.0/book/installation.html#checking-symfony-application-configuration-and-setup).
De volgende directories moeten schrijfbaar zijn voor Symfony

    HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
    sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var web/media
    sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var web/media

Installeer composer (https://getcomposer.org) en voer een composer install uit

    composer install

Voer Doctrine Migrations uit om de database te initialiseren

    php bin/console doctrine:migrations:migrate

Configueer tenslote een vhost van de webserver. Zie ook de specifieke handleiding 
per webserver [in de Symfony 3.0 handleiding](http://symfony.com/doc/3.0/cookbook/configuration/web_server_configuration.html)

Voor productie moet gebruik gemaakt worden van `app_prod.php` i.p.v. `app.php` 
zoals vermeld in de Symfony 3 handleiding.

