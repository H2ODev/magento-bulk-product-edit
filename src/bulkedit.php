<?php
require_once 'app/Mage.php';
require_once "phar://BulkProductEdit.phar/progressbar.class.php";
class Bulkedit{
  public function __construct()
  {
    Mage::app('0');
  }
  public static function run() {
         function getProductSelection(){
            echo "Select Product (empty/sku/sku:sku range): ";
            $handle = fopen ("php://stdin","r");
            $line = fgets($handle);
            if(!trim($line)){
                echo "All Products selected!\n";
                $IDS = array();
                foreach( Mage::getModel('catalog/product')->getCollection() as $prod){
                  array_push($IDS, $prod->getId());
                } 
            }else{
              $input = explode (':', trim($line));
              if (count($input) > 1){
                $return = array();
                for($i=$input[0]; $i<=$input[1]; $i++) {
                  array_push($return, $i);
                }
                $IDS = $return;
              }else{
                $IDS = $input;
              }
            }
            $products = array();
            foreach ($IDS as $productID){
              array_push($products,Mage::getModel('catalog/product')->load($productID));
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
  }
}