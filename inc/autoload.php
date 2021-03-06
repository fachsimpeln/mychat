<?php
     $bd = dirname(__FILE__) . DIRECTORY_SEPARATOR;
     require $bd . 'db.inc.php';
     require $bd . 'login.inc.php';
     require $bd . 'setup.inc.php';

     require $bd . 'cookie' . DIRECTORY_SEPARATOR . 'cookie.inc.php';

     require $bd . 'security' . DIRECTORY_SEPARATOR . 'xss.inc.php';
     require $bd . 'security' . DIRECTORY_SEPARATOR . 'csrf.inc.php';

     require $bd . 'permission' . DIRECTORY_SEPARATOR . 'permission.inc.php';
     require $bd . 'messages' . DIRECTORY_SEPARATOR . 'messages.inc.php';

     require $bd . 'lang' . DIRECTORY_SEPARATOR . 'language.inc.php';
?>
