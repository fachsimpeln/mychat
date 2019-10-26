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
               $message['sender'] = $senderID;
               $message['receiver'] = $receiverID;
               $message['text'] = $text;
               $message['type'] = 'text';
               $message['time'] = time();

               if (!$this->ValidateMessage($message)) {
                    return false;
               }
               $this->message = $message;
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
          * GetFileName
          * Gets filename for message in conversation between $senderID and $receiverID
          *
          * @param int $senderID Internal db user id of sender
          * @param int $receiverID Internal db user id of receiver
          * @return string Filename for message 1.2/2019-10-26.json
          */
          public function GetFileName($senderID, $receiverID) {
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

     }
?>
