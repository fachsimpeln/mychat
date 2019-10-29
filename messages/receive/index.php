<?php

     /**
     * Receive
     * Check for new messages in all chats
     */

     // INITIALIZE
     require '../../inc/db.inc.php';
     require '../../inc/login.inc.php';
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

     // Read all random messages
     $unreadPath = '../messages/user/' . $usrID . '/unread.json';
     $unread = ReadUnreadFromFile($unreadPath, $usrID);
     $output = array();

     $messages = new MessageHandler($pdo);
     foreach ($unread as $key => $value) {
          $lastmessagePath = '../messages/conversation/' . $messages->GetLastMessagePath($usrID, $key);

          $value['lastMessage'] = $messages->ReadLastMessage($lastmessagePath);
          $output[$messages->GetUsername($key)] = $value;
     }

     // Read all last messages from all friends (sort by last messages)
     // Read friends from db

     // Read last message from one chat
     $last_friend = '../messages/conversation/' . $messages->GetLastMessagePath($usrID, $key);




     die(json_encode($output, JSON_PRETTY_PRINT));

     /**
     * ReadUnreadFromFile
     * Reads entry for unread messages
     *
     * @param string $path Path to unread file
     * @param int $usrID Logged in user-id
     * @return array Returns all unread messages (on error null)
     */
     function ReadUnreadFromFile($path, $usrID) {
          $json = null;
          if (file_exists($path)) {
               $json = json_decode(file_get_contents($path), true);
          }
          return $json;
     }

?>
