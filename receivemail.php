#!/usr/local/bin/php -q
<?
require("log.php");

$fd = fopen("php://stdin", "r");
$email = "";
while (!feof($fd)) {
  $email .= fread($fd, 1024);
}
fclose($fd);

addLog("bounce",$email);
?>

