<?php
require_once('app/Mage.php');
umask(0);
Mage::app();
$websiteCodes = array();
$_storeIds = array();
$_productIds = array();
$productsColorsDataList = array();
$i=0;

$product = $product = Mage::getModel('catalog/product')->load(64039);
$colors = array('' => '', 0 => 'Default');
$children = $product->getTypeInstance(true)->getUsedProducts(null, $product);

/** @var Mage_Eav_Model_Config $modelEavConfig */
$modelEavConfig = Mage::getModel('eav/config');
$attribute = $modelEavConfig->getAttribute('catalog_product', 'ids_colour_actual');
/** @var Mage_Eav_Model_Entity_Attribute_Source_Table $source */
$source = $attribute->getSource();
$allOptions = $source->getAllOptions(false);


if(!is_dir('./swatches/')){
    //Directory does not exist, so lets create it.
    mkdir('./swatches', 0755, true);
}

$s3BucketUrl ='http://ids-store.s3.amazonaws.com/media/'; //s3 Bucket url for a specific store
//$source = $s3BucketUrl.'amconf/images'; 

foreach($allOptions as $option){

        $source = $s3BucketUrl.'amconf/images/'.$option['value'].'.jpg'; 
        $color_label = str_replace('/','_',strtolower($option['label'])); 
        $color_label = str_replace(' ','_',$color_label);        
        $destination = 'swatches/'.$color_label.'_'.$option['value'].'.jpg';

        echo $source.'</br>';
        echo $destination.'</br>';


    try{
        //Get the file
        $content = file_get_contents($source);
        //Store in the filesystem.
        $fp = fopen($destination, "w");
        fwrite($fp, $content);
        fclose($fp);    
      }
      //catch exception
      catch(Exception $e) {
        echo 'Message: ' .$e->getMessage();
      }
}
    