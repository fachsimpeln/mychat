<?php

     /**
      * Permission Handler
      * This class handles the permissions for users, groups and admin
      *
      * @author fachsimpeln
      * @category Permission System
      * @version permission.system.db, v1.0, 2019-10-23
      */
     class PermissionHandler
     {

          private $pdo = null; // PDO object for db connection via constructor

          public $errorMessage = null; // Error-Message after error

          /**
          * Constructor
          * Initialize PermissionHandler object
          *
          * @param PDO $pdo PDO with active database connection
          */
          function __construct($pdo)
          {
               $this->pdo = $pdo;
          }

          /**
          * LoginUser
          * Checks if two users are allowed to communicate (are friends)
          *
          * @param string $usrID Internal db user id of sender
          * @param string $receiverUsername Username of receiver
          * @return bool Has permission to write (true), no permission (false)
          */
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
