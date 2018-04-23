<?php
//define (HOST, 'localhost');
//define (USER, 'postgres');
//define (PW, '');
//define (DB, 'hospital');

$db = pg_connect("host=localhost dbname=kernmed port=5432 user=kernmed password=Zul9Zuzlj");

if ($db) {
}
else {
    //exit
    exit(1);
}
?>
