# framework-crawler
Framework爬蟲

使用Symfony 4.2
--------------------------

需要使用到https://packagist.org/packages/symfony/finder
和 http://php.net/manual/en/class.reflectionclass.php

在專案中，
php bin/console doctrine:database:create，建立DataBase
以及
php bin/console doctrine:migrations:migrate，建立table
--------------------------
資料表格式：
id , value, counts, from
