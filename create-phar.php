<?php
$srcRoot = "~/magento-bulk-product-edit/src";
$buildRoot = getenv('CIRCLE_ARTIFACTS');
 
$phar = new Phar($buildRoot . "/BuldProductEdit.phar", 
	FilesystemIterator::CURRENT_AS_FILEINFO |     	FilesystemIterator::KEY_AS_FILENAME, "BuldProductEdit.phar");
$phar["index.php"] = file_get_contents($srcRoot . "/index.php");
$phar["common.php"] = file_get_contents($srcRoot . "/bulkedit.php");
$phar->setStub($phar->createDefaultStub("index.php"));