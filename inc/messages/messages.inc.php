<?php

     /**
      * MessageHandler
      * Prepares messages ready to be send
      *
      * @author fachsimpeln
      * @category Messages
      * @version message.system.db, v1.0, 2019-10-26
      */
     class MessageHandler
     {

          // ERROR MESSAGES
          /** @var string|null Error-Message after error */
          public $errorMessage = null;

          /** @var int Max Message Length */
          private $messageLength = 120;

          /** @var array|null Prepared Message as array */
          public $message = null;

          // PDO VIA CONTRUCTOR
          /** @var PDO|null PDO object for db connection via constructor */
          private $pdo = null;

          /**
          * Constructor
          * Initialize LoginHandler object
          *
          * @param PDO $pdo PDO with active database connection
          */
          public function __construct($pdo)
          {
               $this->pdo = $pdo;
          }

          /**
          * GetUserID
          * Finds usrID based on e-mail or username
          *
          * @param string $user E-Mail or username of user
          * @return int Internal db user id or on error -1
          */
          public function GetUserID($user) {
               $stmt = $this->pdo->prepare("SELECT `usr_id` FROM `mc_users` WHERE (`usr_email`=:user OR `usr_username`=:user)");
               $stmt->bindParam(':user', strval($user));
               $stmt->execute();

               // CHECK IF USER EXISTS
               $rowCount = $stmt->rowCount();
               if ($rowCount == 0) {
                    $this->errorMessage = 'user_does_not_exist';
                    return -1;
               }

               // GET USER INFORMATION FROM DATABASE
               $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

               return $userInfo['usr_id'];
          }

          /**
          * GetUsername
          * Finds username based on usrID
          *
          * @param string $usrID Internal db user id
          * @return string Username of user (on error empty string)
          */
          public function GetUsername($usrID) {
               $stmt = $this->pdo->prepare("SELECT `usr_username` FROM `mc_users` WHERE `usr_id`=:user");
               $stmt->bindParam(':user', strval($usrID));
               $stmt->execute();

               // CHECK IF USER EXISTS
               $rowCount = $stmt->rowCount();
               if ($rowCount == 0) {
                    $this->errorMessage = 'user_does_not_exist';
                    return '';
               }

               // GET USER INFORMATION FROM DATABASE
               $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

               return $userInfo['usr_username'];
          }

          /**
          * PrepareTextMessage
          * Prepares message to be send to receiver
          *
          * @param int $senderID Internal db user id of sender
          * @param int $receiverID Internal db user id of receiver
          * @param string $text Message text to be send
          * @return bool Prepared message saved in attribute $this->message (true) or invalid message (false)
          */
          public function PrepareTextMessage($senderID, $receiverID, $text) {
               $message = array();

               $timeofsend = time();

               $message['id'] = md5($senderID . $receiverID . $timeofsend) . rand(0, getrandmax());
               $message['sender'] = $senderID;
               $message['receiver'] = $receiverID;
               $message['text'] = $text;
               $message['type'] = 'text';
               $message['unread'] = true;
               $message['time'] = $timeofsend;

               if (!$this->ValidateTextMessage($message)) {
                    return false;
               }
               $this->message = json_encode($message);
               return true;
          }

          /**
          * ValidateTextMessage
          * Validates prepared text message by PrepareTextMessage()
          *
          * @param array $message Prepared message as array
          * @return bool Valid message (true) or invalid message (false, error in $this->errorMessage)
          */
          private function ValidateTextMessage($message)
          {
               if (!is_numeric($message['sender']) || !is_numeric($message['receiver'])) {
                    $this->errorMessage = 'invalid_id';
                    return false;
               }
               if ($message['text'] === "" && strlen($message['text']) > $this->messageLength) {
                    $this->errorMessage = 'message_too_long';
                    return false;
               }
               return true;
          }

          /**
          * GetConversationPath
          * Gets filename for message in conversation between $senderID and $receiverID
          *
          * @param int $senderID Internal db user id of sender
          * @param int $receiverID Internal db user id of receiver
          * @return string Filename for message 1.2/2019-10-26.json
          */
          public function GetConversationPath($senderID, $receiverID) {
               $filename = '';
               if (intval($senderID) < intval($receiverID)) {
                    $filename .= $senderID . '.' . $receiverID;
               } else {
                    $filename .= $receiverID . '.' . $senderID;
               }
               $date = date('Y-m-d', time());
               $filename .= '/' . $date . '.json';
               return $filename;
          }

          /**
          * GetLastMessagePath
          * Gets filename for last message in conversation between $senderID and $receiverID
          *
          * @param int $senderID Internal db user id of sender
          * @param int $receiverID Internal db user id of receiver
          * @return string Filename for message 1.2/lastmessage.json
          */
          public function GetLastMessagePath($senderID, $receiverID) {
               $filename = '';
               if (intval($senderID) < intval($receiverID)) {
                    $filename .= $senderID . '.' . $receiverID;
               } else {
                    $filename .= $receiverID . '.' . $senderID;
               }
               $filename .= '/lastmessage.json';
               return $filename;
          }

          /**
          * WriteMessageToFile
          * Appends (with file locking) $message to file at $path
          *
          * @param string $path Path to file
          * @param string $message Text to be appended
          */
          public function WriteMessageToFile($path, $message) {
               if (!file_exists(dirname($path))) {
                    mkdir(dirname($path), 0777, true);
               }

               $f = fopen($path, 'a+');
               if (!($f)) {
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
          public function WriteUnreadToFile($path, $senderID) {
               if (!file_exists(dirname($path))) {
                    mkdir(dirname($path), 0777, true);
               }

               // TIME BASED EDITING
               // (0), 1, (2), 3, (4), 5, (6), 7, (8), 9
               $tries = 0;
               while (intval(substr(time(), -1) % 2) == 0) {
                    // WAIT
                    $tries += 1;
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
          public function OverwriteFile($path, $message) {
               if (!file_exists(dirname($path))) {
                    mkdir(dirname($path), 0777, true);
               }

               $f = fopen($path, 'w');
               if (!($f)) {
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

          /**
          * ReadLastMessage
          * Reads last message in chat and returns last message as array
          *
          * @param string $path Path to file
          * @return array Last message read from file (on error empty array)
          */
          public function ReadLastMessage($path) {
               if (file_exists($path) && filesize($path) > 0) {
                    $lastMessage = "";
                    while ($lastMessage == "") {
                         $lastMessage = file_get_contents($path);
                    }
                    return json_decode($lastMessage, true);
               }
               return array();
          }

          public function ReadConversation($path)
          {
               // code...
          }

     }
?>
