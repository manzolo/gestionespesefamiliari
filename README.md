# gestionespesefamiliari
Gestione spese familiari (family expenses management)

[![Build Status](https://travis-ci.org/manzolo/gestionespesefamiliari.svg?branch=master)]
(https://travis-ci.org/manzolo/gestionespesefamiliari) [![Coverage Status](https://img.shields.io/coveralls/manzolo/gestionespesefamiliari.svg)] 
(https://coveralls.io/r/manzolo/gestionespesefamiliari)

##Installazione:
```
cp app/config/parameters.yml.dist app/config/parameters.yml
composer install
```
###Installazione utente amministratore
```
php app/console fifree2:install admin admin admin@admin.it
```
###Installazione dati di base e di prova
```
php app/console gestionespese:installdefaultdata
```
###Se si utilizza il database sqlite
```
chmod +w app/cache/dbtest.sqlite
```


