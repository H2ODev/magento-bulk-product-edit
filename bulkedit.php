<?php
(PHP_SAPI !== 'cli' || isset($_SERVER['HTTP_USER_AGENT'])) && die('cli only');
require_once 'app/Mage.php';
Mage::app('0');

function promptOrdernumber(){
  echo "Order number: ";
  $handle = fopen ("php://stdin","r");
  $line = fgets($handle);
  if(!trim($line)){
      echo "No Order number provided!\n";
      promptOrdernumber();
  }
  if(trim($line) == "exit"){
    exit;
  }
  $order = Mage::getModel('sales/order')->loadByIncrementId(trim($line));
  if (!$order->getIncrementId()){
    echo "Order not found\n";
    promptOrdernumber();
  }
  echo $order->getCustomerEmail() . "\n";
  Mage::getModel('H2ODev_MagentoOrderExport_Model_Export')->export($order);
  promptOrdernumber();
}
promptOrdernumber();