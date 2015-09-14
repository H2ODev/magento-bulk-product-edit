<?php
require_once '../app/Mage.php';
Mage::app('0');
require_once 'src/progressbar.class.php';
function getProductSelection(){
  echo "Select Product (empty/sku/sku:sku range): ";
  $handle = fopen ("php://stdin","r");
  $line = fgets($handle);
  if(!trim($line)){
      echo "All Products selected!\n";
      return Mage::getModel('catalog/product')->getCollection();
  }
  $products = array();
  foreach (explode (':', trim($line)) as $productID){
    array_push($products,Mage::getModel('catalog/product')->loadByAttribute('sku',$productID));
  }
  return $products;
}

$products = getProductSelection();
$progressBar = new ProgressBar(count($products));

foreach($products as $product) {
  $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
  if (!$stockItem->getId()) {
      $stockItem->setData('product_id', $product->getId());
      $stockItem->setData('stock_id', 1); 
  }
  $stockItem->setData('manage_stock', 1);
  $stockItem->setData('use_config_manage_stock', 1);
  $stockItem->setData('is_in_stock', 1);
  $stockItem->setData('use_config_notify_stock_qty', 1);
  $stockItem->setData('use_config_max_sale_qty', 1);
  $stockItem->setData('use_config_min_sale_qty', 1);
  $stockItem->setData('use_config_backorders', 1);
  $stockItem->setData('use_config_min_qty', 1);
  $stockItem->setData('use_config_enable_qty_inc', 1);
  try {
    $stockItem->save();
    $product->save();
    echo $progressBar->drawCurrentProgress();
  } catch (Exception $e) {
      echo "{$e}";
  }  
}
echo "Edited ".count($products) . " products\n";