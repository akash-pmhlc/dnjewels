<?php
		$installer = $this;
		$installer->startSetup();
		
$installer->addAttribute("catalog_product", "rates", array("type"=>"varchar"));
$installer->addAttribute("catalog_product", "stonerates", array("type"=>"varchar"));
$installer->addAttribute("catalog_product", "goldpurity", array("type"=>"varchar"));
$installer->addAttribute("catalog_product", "goldcolor", array("type"=>"varchar"));
$installer->addAttribute("catalog_product", "goldrate14k", array("type"=>"varchar"));
$installer->addAttribute("catalog_product", "goldrate18k", array("type"=>"varchar"));
$installer->addAttribute("catalog_product", "goldweight", array("type"=>"varchar"));
$installer->addAttribute("catalog_product", "totalweight", array("type"=>"varchar"));
$installer->addAttribute("catalog_product", "labor", array("type"=>"varchar"));
$installer->addAttribute("catalog_product", "rateids", array("type"=>"varchar"));
$installer->addAttribute("catalog_product", "stoneids", array("type"=>"varchar"));
$installer->addAttribute("catalog_product", "certificateids", array("type"=>"varchar"));
$installer->addAttribute("catalog_product", "taxrate", array("type"=>"varchar"));
$installer->addAttribute("catalog_product", "diamondcharge", array("type"=>"varchar"));
$installer->addAttribute("catalog_product", "stonecharge", array("type"=>"varchar"));
$installer->addAttribute("catalog_product", "certificatecharge", array("type"=>"varchar"));
$installer->addAttribute("catalog_product", "listingprice", array("type"=>"varchar"));
$installer->addAttribute("catalog_product", "variablecharge", array("type"=>"varchar"));
		$installer->endSetup();
			 