<?php
     $bd = dirname(__FILE__) . DIRECTORY_SEPARATOR;
     require $bd . 'db.inc.php';
     require $bd . 'login.inc.php';
     require $bd . 'setup.inc.php';

     require $bd . 'security/xss.inc.php';

     require $bd . 'permission/permission.inc.php';
     require $bd . 'messages/messages.inc.php';

     require $bd . 'lang/language.inc.php';
?>
