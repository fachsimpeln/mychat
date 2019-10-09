<?php
     /**
      * Login Handler
      */
     class LoginHandler
     {
          // USER INFO
          public $usrID = null;
          public $usrUsername = null;

          // ERROR MESSAGES
          public $errorMessage = null;

          // PDO VIA CONTRUCTOR
          private $pdo = null;

          public function __construct($pdo)
          {
               $this->pdo = $pdo;
          }

          public function LoginUser($email, $password)
          {

               $stmt = $this->pdo->prepare("SELECT `usr_id`, `usr_username`, `usr_password` FROM `mc_users` WHERE `usr_email`=:email");
               $stmt->bindParam(':email', strval($email));
               $stmt->execute();

               // CHECK IF USER EXISTS
               $rowCount = $stmt->rowCount();
               if ($rowCount == 0) {
                    $this->errorMessage = 'user_does_not_exist';
                    return false;
               }

               // GET USER INFORMATION FROM DATABASE
               $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

               if (password_verify($password, $userInfo['usr_password'])) {
                    $this->usrID = $userInfo['usr_id'];
                    $this->usrUsername = $userInfo['usr_username'];
                    return true;
               }
               $this->errorMessage = 'wrong_password';
               return false;
          }



     }
?>
