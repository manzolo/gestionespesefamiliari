# gestionespesefamiliari
Gestione spese familiari (family expenses management)

[![Build Status](https://travis-ci.com/manzolo/gestionespesefamiliari.svg?branch=master)](https://travis-ci.com/manzolo/gestionespesefamiliari)
[![Coverage Status](https://img.shields.io/coveralls/manzolo/gestionespesefamiliari.svg)](https://coveralls.io/r/manzolo/gestionespesefamiliari)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/manzolo/gestionespesefamiliari/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/manzolo/gestionespesefamiliari/?branch=master)

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
chmod +w app/tmp/dbtest.sqlite
```
###Per altri database (mysql, prostgres) modificare file 
```
app/config/parameters.yml
```




