<?php

     // INITIALIZE
     require '../../inc/db.inc.php';
     require '../../inc/login.inc.php';
     require '../../inc/security/xss.inc.php';
     $pdo = DatabaseConnection::connect();

     // CHECK MESSAGE LENGTH
     $message = $_REQUEST['message'];
     if (strlen($message) > 120) {
          die('message_too_long');
     }

     // CHECK USER AUTH
     $loginHandler = new LoginHandler($pdo);

     $loginInfo = $loginHandler->GetToken($_POST);
     if (!$loginHandler->LoginToken($loginInfo['lid'], $loginInfo['lto'])) {
          // NOT AUTHENTICATED
          die('no_auth');
     }
     // GET USR-ID
     $usrID = $loginHandler->usrID;

     // RECEIVER TYPE (GROUP, PRIVATE, PUBLIC)
     $receiverType = $_REQUEST['rtype'];
     $receiverUsername = $_REQUEST['receiver'];

     // CHECK PERMISSION
     $permissions = new PermissionHandler($pdo);
     if ($receiverType === 'private') {
          // CHECK IF RIGHT TO SEND
          if (!$permissions->GetChatPermission($usrID, $receiverUsername)) {
               // NO PERMISSION
               die('no_permission');
          }
          // SEND MESSAGE

     }


?>
