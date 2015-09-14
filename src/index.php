<?php
(PHP_SAPI !== 'cli' || isset($_SERVER['HTTP_USER_AGENT'])) && die('cli only');
require_once "phar://BulkProductEdit.phar/bulkedit.php";
$bulk = new Bulkedit();
$bulk->run();