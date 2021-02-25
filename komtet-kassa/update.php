<?php

$dbQuery = DB::query("SHOW COLUMNS FROM `".PREFIX."atol` LIKE 'request'");
if(!$row = DB::fetchArray($dbQuery)) {
  DB::query("ALTER TABLE `".PREFIX."atol` ADD `request` LONGTEXT COMMENT 'Содержимое заказа'");
}