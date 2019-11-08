<?php

     // INITIALIZE
     require '../../inc/autoload.php';
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

     // CHECK PERMISSION
     $permissions = new PermissionHandler($pdo);
     if ($receiverType === 'private') {
          // CHECK IF RIGHT TO SEND
          if (!$permissions->GetChatPermission($usrID, $receiverUsername)) {
               // NO PERMISSION
               die('no_permission');
          }
          $text = $_REQUEST['message'];

          $messages = new MessageHandler($pdo);
          $receiverID = $messages->GetUserID($receiverUsername);

          $conversationPath = '../messages/conversation/' . $messages->GetConversationPath($usrID, $receiverID);

          $messages->ReadConversation($usrID, $receiverID);

          /* Output all unread messages and then up to x more (e.g. 15)
           * Then lazyload new message via an offset parameter (handled by javascript)
           */


     }


?>
