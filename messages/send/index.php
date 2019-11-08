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
          $text = $_REQUEST['message'];

          $messages = new MessageHandler($pdo);
          $receiverID = $messages->GetUserID($receiverUsername);
          if (!$messages->PrepareTextMessage($usrID, $receiverID, $text)) {
               // INVALID MESSAGE
               die($messages->errorMessage);
          }

          $conversationPath = '../messages/conversation/' . $messages->GetConversationPath($usrID, $receiverID);

          $lastmessagePath = '../messages/conversation/' . $messages->GetLastMessagePath($usrID, $receiverID);

          $unreadPath = '../messages/user/' . $receiverID . '/unread.json';

          // To read as one JSON use '{"chat": [' . $lines remove last comma . ']}'
          $messages->WriteMessageToFile($conversationPath, $messages->message . ',');
          $messages->WriteUnreadToFile($unreadPath, $usrID);

          $messages->OverwriteFile($lastmessagePath, $messages->message);

          die('message_sent');
     }

?>
