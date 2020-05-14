<?php
include_once("utils.php");
//prepare to session migration, means create a temporary cookie that does a job of a bridge
$result = SessionMigrationHanlder::getInstance()->prepare();
//send a respose text yes
echo $result;
?>