<?php
$srcRoot = "src";
$buildRoot = "build";
 
$phar = new Phar($buildRoot . "/BulkProductEdit.phar", 
	FilesystemIterator::CURRENT_AS_FILEINFO |     	FilesystemIterator::KEY_AS_FILENAME, "BulkProductEdit.phar");
$phar["index.php"] = file_get_contents($srcRoot . "/index.php");
$phar["bulkedit.php"] = file_get_contents($srcRoot . "/bulkedit.php");
$phar["progressbar.class.php"] = file_get_contents($srcRoot . "/progressbar.class.php");
$phar->setStub($phar->createDefaultStub("index.php"));