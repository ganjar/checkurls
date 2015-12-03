# checkurls
Script for recursively check site urls

*Example for run*: php checkurls.php -u 'http://site.com/' > result.csv

## Result example:
url; status; location

http://site.com/; 200;

http://site.com/test; 301; http://site.com/new-test/
