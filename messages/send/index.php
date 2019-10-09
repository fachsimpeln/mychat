<?php

     // INITIALIZE
     require '../../inc/db.inc.php';
     require '../../inc/login.inc.php';
     require '../../inc/security/xss.inc.php';
     $pdo = DatabaseConnection::connect();

     // CHECK USER AUTH
     $loginHandler = new LoginHandler($pdo);

     $loginInfo = $loginHandler->GetToken($_POST);
     if (!$loginHandler->LoginToken($loginInfo['lid'], $loginInfo['lto'])) {
          // NOT AUTHENTICATED
          die('no_auth');
     }
     // GET USR-ID
     $usrID = $loginHandler->usrID;

     // METHOD (GROUP, PRIVATE, PUBLIC)

     // CHECK PERMISSION

?>
