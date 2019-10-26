<?php

     // INITIALIZE
     require '../../inc/db.inc.php';
     require '../../inc/login.inc.php';
     require '../../inc/permission/permission.inc.php';
     require '../../inc/messages/messages.inc.php';
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
          WriteMessageToFile($conversationPath, $messages->message . ',');
          WriteMessageToFile($unreadPath, $messages->message . ',');

          OverwriteFile($lastmessagePath, $messages->message);

          die('message_sent');
     }

     /**
     * WriteMessageToFile
     * Appends (with file locking) $message to file at $path
     *
     * @param string $path Path to file
     * @param string $message Text to be appended
     */
     function WriteMessageToFile($path, $message) {
          if (!($f = fopen($path, 'a+'))) {
               die('could_not_open_file');
          }
          if (flock($f, LOCK_EX)) {
               fwrite($f, $message . PHP_EOL);
               flock($f, LOCK_UN);
          } else {
               // Could not aquire lock
               die('could_not_open_file');
          }
          fclose($f);
     }

     /**
     * OverwriteFile
     * Overwrite a file (with file locking) to $message at $path
     *
     * @param string $path Path to file
     * @param string $message Text to be written
     */
     function OverwriteFile($path, $message) {
          if (!($f = fopen($path, 'w'))) {
               die('could_not_open_file');
          }
          if (flock($f, LOCK_EX)) {
               fwrite($f, $message);
               flock($f, LOCK_UN);
          } else {
               // Could not aquire lock
               die('could_not_open_file');
          }
          fclose($f);
     }

?>
