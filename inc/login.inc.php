<?php
     /**
      * Login Handler
      */
     class LoginHandler
     {
          // USER INFO
          public $usrID = null;
          public $usrUsername = null;

          // CONFIGURATION
          private $maxAttempts = 5;
          private $attemptTime = 10;

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
               // STRIP WHITESPACE FROM BEGINNING AND END OF EMAIL
               $email = trim($email);

               $stmt = $this->pdo->prepare("SELECT `usr_id`, `usr_username`, `usr_password` FROM `mc_users` WHERE `usr_email`=:email");
               $stmt->bindParam(':email', strval($email));
               $stmt->execute();

               // CHECK IF USER EXISTS
               $rowCount = $stmt->rowCount();
               if ($rowCount == 0) {
                    // ADD ATTEMPT FOR UNKNOWN USER
                    AddAttempt($email);

                    $this->errorMessage = 'user_does_not_exist';
                    return false;
               }

               // GET USER INFORMATION FROM DATABASE
               $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

               if (!ValidateAttempt($email, $userInfo['usr_id'])) {
                    die('brute-force-protection-' . $this->attemptTime . '-min');
               }

               if (password_verify($password, $userInfo['usr_password'])) {
                    $this->usrID = $userInfo['usr_id'];
                    $this->usrUsername = $userInfo['usr_username'];

                    ResetAttempts($email, $this->usrID);

                    return true;
               }
               AddAttempt($email, $userInfo['usr_id']);

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

          public function SetToken($loginIdentifier, $loginToken, $expires)
          {
               setcookie("mc_lid", $loginIdentifier, intval($expires));
               setcookie("mc_lto", $loginToken, intval($expires));
          }

          public function GetToken($POSTParams)
          {
               $loginInfo = array();
               if (isset($_COOKIE["mc_lid"]) && isset($_COOKIE["mc_lto"])) {
                    $loginInfo['loginIdentifier'] = $_COOKIE["mc_lid"];
                    $loginInfo['loginToken'] = $_COOKIE["mc_lto"];
                    return $loginInfo;
               }
               if (isset($POSTParams['lid']) && isset($POSTParams['lto'])) {
                    $loginInfo['loginIdentifier'] = $POSTParams['lid'];
                    $loginInfo['loginToken'] = $POSTParams['lto'];
                    return $loginInfo;
               }
               return null;
          }

          // Brute-Force Protection
          // https://www.owasp.org/index.php/Slow_Down_Online_Guessing_Attacks_with_Device_Cookies

          private function ValidateAttempt($user, $usrID = -1)
          {
               if (isset($_COOKIE['mc_dc']) && $usrID != -1) {
                    // CHECK DEVICE COOKIE
                    $devicecookie = $_COOKIE['mc_dc'];
                    if (ValidateDeviceCookie($usrID, $devicecookie)) {
                         if (CheckDeviceCookie($usrID, $devicecookie)) {
                              return true;
                         } else {
                              return false;
                         }
                    }
               }
               if (CheckUnknownUser($user)) {
                    return true;
               } else {
                    return false;
               }
               return false;
          }

          private function AddAttempt($user, $usrID = -1) {
               if (isset($_COOKIE['mc_dc']) && $usrID != -1) {
                    AddDeviceAttempt($usrID, $user);
               }
               AddUnknownAttempt($user);
          }

          private function AddDeviceAttempt($usrID, $user)
          {
               $devicecookie = $_COOKIE['mc_dc'];

               if (ValidateDeviceCookie($usrID, $devicecookie)) {

                    $stmt = $this->pdo->prepare("UPDATE `mc_devicecookies` SET `dc_attempts`=`dc_attempts`+1 WHERE `usr_id`=:usrid AND `dc_token`=:token");

                    $stmt->bindParam(':usrid', strval($usrID));
                    $stmt->bindParam(':token', strval($devicecookie));
                    $stmt->execute();

                    // UPDATE LOCKED TIME
                    $stmt = $this->pdo->prepare("UPDATE `mc_devicecookies` SET `dc_locked_until`=(CURRENT_TIMESTAMP + INTERVAL '" . $this->attemptTime . "' MINUTE) WHERE `dc_attempts` >= :max AND `dc_locked_until` IS NULL");
                    $stmt->bindParam(':max', intval($this->maxAttempts));
                    $stmt->execute();

                    return true;
               }
               AddUnknownAttempt($user);
               return false;
          }

          private function AddUnknownAttempt($user)
          {
               // CHECK IF ALREADY IN DB
               $stmt = $this->pdo->prepare("SELECT `fl_id` FROM `mc_loginsfailed` WHERE `fl_user`=:user");
               $stmt->bindParam(':user', strval($user));
               $stmt->execute();

               // ADD ATTEMPT
               $rowCount = $stmt->rowCount();
               if ($rowCount == 0) {
                    $stmt = $this->pdo->prepare("INSERT INTO `mc_loginsfailed`(`fl_user`) VALUES (:user)");
               } else {
                    $stmt = $this->pdo->prepare("UPDATE `mc_loginsfailed` SET `fl_attempts`=`fl_attempts`+1 WHERE `fl_user`=:user");
               }
               $stmt->bindParam(':user', strval($user));
               $stmt->execute();

               // UPDATE LOCKED TIME
               $stmt = $this->pdo->prepare("UPDATE `mc_loginsfailed` SET `fl_locked_until`=(CURRENT_TIMESTAMP + INTERVAL '" . $this->attemptTime . "' MINUTE) WHERE `fl_attempts` >= :max AND `fl_locked_until` IS NULL");
               $stmt->bindParam(':max', intval($this->maxAttempts));
               $stmt->execute();
          }

          private function CheckUnknownUser($user) {
               $stmt = $this->pdo->prepare("SELECT `fl_id` FROM `mc_loginsfailed` WHERE (`fl_user`=:user AND (`fl_locked_until` IS NULL OR `fl_locked_until` < CURRENT_TIMESTAMP))");
               $stmt->bindParam(':user', strval($user));
               $stmt->execute();

               // CHECK IF LOGIN EXISTS
               $rowCount = $stmt->rowCount();
               if ($rowCount == 0) {
                    return false;
               }
               return true;
          }

          private function ValidateDeviceCookie($usrID, $devicecookie)
          {
               $stmt = $this->pdo->prepare("SELECT `dc_id` FROM `mc_devicecookies` WHERE (`usr_id`=:usrid AND `dc_token`=:token)");
               $stmt->bindParam(':usrid', strval($usrID));
               $stmt->bindParam(':token', strval($devicecookie));
               $stmt->execute();

               // CHECK IF LOGIN EXISTS
               $rowCount = $stmt->rowCount();
               if ($rowCount == 0) {
                    return false;
               }
               return true;
          }

          private function CheckDeviceCookie($usrID, $devicecookie)
          {
               $stmt = $this->pdo->prepare("SELECT `dc_id` FROM `mc_devicecookies` WHERE (`usr_id`=:usrid AND `dc_token`=:token AND (`dc_locked_until` IS NULL OR `dc_locked_until` < CURRENT_TIMESTAMP))");
               $stmt->bindParam(':usrid', strval($usrID));
               $stmt->bindParam(':token', strval($devicecookie));
               $stmt->execute();

               // CHECK IF LOGIN EXISTS
               $rowCount = $stmt->rowCount();
               if ($rowCount == 0) {
                    return false;
               }
               return true;
          }

          private function ResetAttempts($user, $usrID) {
               $devicecookie = $_COOKIE['mc_dc'];
               if (ValidateDeviceCookie($usrID, $devicecookie)) {
                    IssueNewDeviceCookie($usrID, true);
                    return;
               }
               $stmt = $this->pdo->prepare("UPDATE `mc_loginsfailed` SET `fl_attempts`=0, `fl_locked_until`=NULL WHERE `fl_user`=:user");
               $stmt->bindParam(':user', strval($user));
               $stmt->execute();

               IssueNewDeviceCookie($usrID);
          }

          private function IssueNewDeviceCookie($usrID, $withcookie = false)
          {
               // GENERATE NEW DEVICE COOKIE
               $crypto = true;
               $devicecookie = bin2hex(openssl_random_pseudo_bytes(64, $crypto));

               // REMOVE OLD DEVICE COOKIE FROM DB AND ADD NEW ONE
               if ($withcookie) {
                    $stmt = $this->pdo->prepare("UPDATE `mc_devicecookies` SET `dc_token`=:newtoken, `dc_attempts`=0, `dc_locked_until`=NULL WHERE (`usr_id`=:usrid AND `dc_token`=:oldtoken)");

                    $stmt->bindParam(':oldtoken', strval($_COOKIE['mc_dc']));
               } else {
                    $stmt = $this->pdo->prepare("INSERT INTO `mc_devicecookies`(`dc_token`, `usr_id`) VALUES (:newtoken, :usrid)");
               }

               $stmt->bindParam(':usrid', strval($usrID));
               $stmt->bindParam(':newtoken', strval($devicecookie));
               $stmt->execute();

               // SET COOKIE TO USERS BROWSER
               $expires = time();
               $expires += 24 * 60 * 60 * 182; // 24h * 182 = 182 Days = half year
               setcookie("mc_dc", $devicecookie, intval($expires));
          }



     }
?>
