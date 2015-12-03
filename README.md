# СheckUrls
Console script for recursively check site urls

###Example for run:
```sh
php checkurls.php -u 'http://site.com/' > site.csv
```
If you need test many sites you may run from several **-u** params or execute from several unix threads
```sh
php checkurls.php -u 'http://site.com/' > site.csv &
php checkurls.php -u 'http://site2.com/' > site2.csv &
```

## Result example:
url; status; location

http://site.com/; 200;

http://site.com/test; 301; http://site.com/new-test/
