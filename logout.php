<?php
require('functions.php');
if (isset($_SERVER['HTTP_REFERER'])) $ref = $_SERVER['HTTP_REFERER'];
else $ref = '.';
session_start();
clearUserSetting(getLoggedInUser(),'session-token');
setcookie('session',"",1);
unset($_SESSION);
session_destroy();
header("Location:".$ref);

?>