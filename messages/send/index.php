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
          WriteUnreadToFile($unreadPath, $usrID);

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
          if (!file_exists(dirname($path))) {
               mkdir(dirname($path), 0777, true);
          }

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
     * WriteUnreadToFile
     * Add/Changes entry for unread messages
     * (with time-based lock)
     *
     * @param string $path Path to unread file
     * @param int $senderID Sender id for message
     */
     function WriteUnreadToFile($path, $senderID) {
          if (!file_exists(dirname($path))) {
               mkdir(dirname($path), 0777, true);
          }

          // TIME BASED EDITING
          // (0), 1, (2), 3, (4), 5, (6), 7, (8), 9
          while (intval(substr(time(), -1) % 2) == 0) {
               // WAIT
          }
          $json = array();
          if (file_exists($path)) {
               $json = json_decode(file_get_contents($path), true);
          }
          if (isset($json[$senderID])) {
               $json[$senderID]['unread'] += 1;
          } else {
               $json[$senderID]['unread'] = 1;
          }
          file_put_contents($path, json_encode($json));
     }

     /**
     * OverwriteFile
     * Overwrite a file (with file locking) to $message at $path
     *
     * @param string $path Path to file
     * @param string $message Text to be written
     */
     function OverwriteFile($path, $message) {
          if (!file_exists(dirname($path))) {
               mkdir(dirname($path), 0777, true);
          }

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
