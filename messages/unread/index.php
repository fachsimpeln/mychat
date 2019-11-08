<?php

     /**
     * Unread
     * Check for new unread messages in all chats
     */

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

     $unreadPath = '../messages/user/' . $usrID . '/unread.json';
     $unread = ReadUnreadFromFile($unreadPath, $usrID);
     $output = array();

     if ($unread == null || count($unread) == 0) {
          die(json_encode($output, JSON_PRETTY_PRINT));
     }

     $messages = new MessageHandler($pdo);
     foreach ($unread as $key => $value) {
          $lastmessagePath = '../messages/conversation/' . $messages->GetLastMessagePath($usrID, $key);

          $value['lastMessage'] = $messages->ReadLastMessage($lastmessagePath);
          $output[$messages->GetUsername($key)] = $value;
     }
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
