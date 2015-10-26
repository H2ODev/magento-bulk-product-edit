<?php
require_once 'app/Mage.php';
require_once "phar://BulkProductEdit.phar/progressbar.class.php";
class Bulkedit{
  private $access_token = "0745f5b5-61c7-48f4-b6f2-22d4ec7dc520";
  public function __construct()
  {
    Mage::app('0');
  }
  public function run() {

          $products = $this->getProductSelection();
          $progressBar = new ProgressBar(count($products));
          $countProducts = count($products);

          foreach($products as $product) {
            $oldISBN = $product->getIsbn();
            $title = urlencode($product->getName());
            $url = "http://api.vlb.de/api/v1/products?search=IS=" . $oldISBN . "%20TI=" . $title . "&access_token=" . $this->access_token;
            $response = json_decode($this->callAPI('GET',$url));

            if (count($response->content) > 1){
              echo $progressBar->drawCurrentProgress();
              var_dump("Failed asserting: " . $product->getId());
              $countProducts--;
              continue;
            }else if (count($response->content) == 0){
              echo $progressBar->drawCurrentProgress();
              $countProducts--;
              continue;
            }
            if ($oldISBN != current($response->content)->identifier){
              $product->setIsbn(current($response->content)->identifier);
              $product->setEan(current($response->content)->identifier);
              try {
                $product->save();
                echo $progressBar->drawCurrentProgress();
              } catch (Exception $e) {
                  echo "{$e}";
              } 
            }else{
              echo $progressBar->drawCurrentProgress();
              $countProducts--;
            }
          }
          echo "Edited ".$countProducts . " products\n";
  }
  private function getProductSelection(){
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
  private function callAPI($method, $url, $data = false)
  {
      $curl = curl_init();

      switch ($method)
      {
          case "POST":
              curl_setopt($curl, CURLOPT_POST, 1);

              if ($data)
                  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
              break;
          case "PUT":
              curl_setopt($curl, CURLOPT_PUT, 1);
              break;
          default:
              if ($data)
                  $url = sprintf("%s?%s", $url, http_build_query($data));
      }
      curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json-short'));
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      $result = curl_exec($curl);

      curl_close($curl);

      return $result;
  }
}