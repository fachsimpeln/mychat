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

          public function LoginToken($loginIdentifier, $loginToken)
          {

               $stmt = $this->pdo->prepare("SELECT `login_userIdentifier`, `login_token`, `login_expires`, `usr_id` FROM `mc_logins` WHERE `login_userIdentifier`=:loginUserID");
               $stmt->bindParam(':loginUserID', strval($loginIdentifier));
               $stmt->execute();

               // CHECK IF LOGIN EXISTS
               $rowCount = $stmt->rowCount();
               if ($rowCount == 0) {
                    $this->errorMessage = 'session_does_not_exist';
                    return false;
               }

               // GET LOGIN INFORMATION FROM DATABASE
               $loginInfo = $stmt->fetch(PDO::FETCH_ASSOC);

               // CHECK EXPIRE
               if (intval($loginInfo['login_expires']) < time()) {
                    $this->errorMessage = 'session_expired';
                    return false;
               }

               if (hash_equals($loginToken, $loginInfo['login_token'])) {
                    $stmt = $this->pdo->prepare("SELECT `usr_email`, `usr_username`, `usr_password` FROM `mc_users` WHERE `usr_id`=:usrid");
                    $stmt->bindParam(':usrid', strval($loginInfo['usr_id']));
                    $stmt->execute();

                    // CHECK IF USER EXISTS
                    $rowCount = $stmt->rowCount();
                    if ($rowCount == 0) {
                         $this->errorMessage = 'user_does_not_exist';
                         return false;
                    }

                    // GET USER INFORMATION FROM DATABASE
                    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

                    $this->usrID = $userInfo['usr_id'];
                    $this->usrUsername = $userInfo['usr_username'];
                    return true;
               }
               $this->errorMessage = 'wrong_session_token';
               return false;
          }

     }
?>
