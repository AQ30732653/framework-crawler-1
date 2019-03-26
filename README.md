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
-----------------------
這個datatable的bundle....，我有點研究不出來，後來改傳統的
視覺化-DataTable：https://omines.github.io/datatables-bundle/
-----------------------
我有使用Symfony encore，但可以不執行....因為我不太會用。
參考：https://symfony.com/doc/current/frontend.html
