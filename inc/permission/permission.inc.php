<?php

     /**
      * Permission Handler
      */
     class PermissionHandler
     {

          private $pdo = null;

          // ERROR MESSAGES
          public $errorMessage = null;

          function __construct($pdo)
          {
               $this->pdo = $pdo;
          }

          public function GetChatPermission($usrID, $receiverUsername)
          {
               // CHECK IF THE TWO ARE FRIENDS
               $stmt = $this->pdo->prepare("SELECT `fr_id` FROM `mc_friendlist` WHERE ((`usr_id1`=:senderid OR `usr_id2`=:senderid) AND (`usr_id1`= (SELECT `usr_id` FROM `mc_users` WHERE `usr_username`=:receiverUsername) OR `usr_id2`=(SELECT `usr_id` FROM `mc_users` WHERE `usr_username`=:receiverUsername))) AND `fr_accepted`=1");

               $stmt->bindParam(':senderid', strval($usrID));
               $stmt->bindParam(':receiverUsername', strval($receiverUsername));
               $stmt->execute();

               // CHECK IF USER EXISTS
               $rowCount = $stmt->rowCount();
               if ($rowCount == 0) {
                    $this->errorMessage = 'no_permission';
                    return false;
               }

               return true;
          }

          public function SetUserPermission($usrID, $permission)
          {

          }

     }


?>
