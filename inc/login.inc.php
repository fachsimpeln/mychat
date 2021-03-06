<?php
     /**
      * LoginHandler
      * Handles the login process via username, email, password and/or tfa
      * Also handles the login via the session tokens
      *
      * @author fachsimpeln
      * @category Login System
      * @version login.system.db, v1.0, 2019-10-22
      */
     class LoginHandler
     {
          /** @var int|null Internal DB User-ID */
          public $usrID = null;

          /** @var string|null  User-Name of user */
          public $usrUsername = null;

          // CONFIGURATION
          /** @var int brute-force protection after x tries */
          private $maxAttempts = 5;

          /** @var int time to lock account for in minutes */
          private $attemptTime = 10; // in minutes

          // ERROR MESSAGES
          /** @var string|null Error-Message after error */
          public $errorMessage = null;

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
          * LoginUser
          * User Login via username/email and password
          *
          * @param string $user Username / E-Mail of user
          * @param string $password Password of user
          * @return bool Login success (true -> successful)
          */
          public function LoginUser($user, $password)
          {
               // STRIP WHITESPACE FROM BEGINNING AND END OF EMAIL
               $user = trim($user);

               $stmt = $this->pdo->prepare("SELECT `usr_id`, `usr_username`, `usr_password` FROM `mc_users` WHERE (`usr_email`=:user OR `usr_username`=:user)");
               $stmt->bindParam(':user', strval($user));
               $stmt->execute();

               // GET USER INFORMATION FROM DATABASE
               $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

               if (!$this->ValidateAttempt($user, $userInfo['usr_id'])) {
                    $this->errorMessage = 'brute_force_protection_' . $this->attemptTime . '_min';
                    return false;
               }

               // CHECK IF USER EXISTS
               $rowCount = $stmt->rowCount();
               if ($rowCount == 0) {
                    // ADD ATTEMPT FOR UNKNOWN USER
                    $this->AddAttempt($user);

                    $this->errorMessage = 'user_does_not_exist';
                    return false;
               }

               if (password_verify($password, $userInfo['usr_password'])) {
                    $this->usrID = $userInfo['usr_id'];
                    $this->usrUsername = $userInfo['usr_username'];

                    $this->ResetAttempts($user, $this->usrID);

                    return true;
               }
               $this->AddAttempt($user, $userInfo['usr_id']);

               $this->errorMessage = 'wrong_password';
               return false;
          }

          /**
          * LoginToken
          * User Login via session tokens
          *
          * @param string $loginIdentifier Random string for user identification in cookies ['mc_lid']
          * @param string $loginToken Random string for user authorization in cookies ['mc_lto']
          * @return bool Login success (true -> successful)
          */
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
                    $stmt = $this->pdo->prepare("SELECT `usr_id`, `usr_email`, `usr_username`, `usr_password` FROM `mc_users` WHERE `usr_id`=:usrid");
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

          /**
          * SetToken
          * Saves the session token in the browser cookies
          *
          * @param string $loginIdentifier Random string for user identification in cookies ['mc_lid']
          * @param string $loginToken Random string for user authorization in cookies ['mc_lto']
          * @param int $expires Cookie expiration as UNIX timestamp
          */
          public function SetToken($loginIdentifier, $loginToken, $expires)
          {
               CookieHandler::SetCookie("mc_lid", $loginIdentifier, true, intval($expires));
               CookieHandler::SetCookie("mc_lto", $loginToken, true, intval($expires));
          }

          /**
          * GetToken
          * Saves the session token in the browser cookies
          *
          * @param array $POSTParams POST-Parameters of request
          * @return array LoginInfo with loginIdentifier and loginToken
          */
          public function GetToken($POSTParams)
          {
               $loginInfo = array();
               if (isset($_COOKIE["mc_lid"]) && isset($_COOKIE["mc_lto"])) {
                    $loginInfo['lid'] = $_COOKIE["mc_lid"];
                    $loginInfo['lto'] = $_COOKIE["mc_lto"];
                    return $loginInfo;
               }
               if (isset($POSTParams['lid']) && isset($POSTParams['lto'])) {
                    $loginInfo['lid'] = $POSTParams['lid'];
                    $loginInfo['lto'] = $POSTParams['lto'];
                    return $loginInfo;
               }
               return null;
          }

          // Brute-Force Protection
          // https://www.owasp.org/index.php/Slow_Down_Online_Guessing_Attacks_with_Device_Cookies

          /**
          * ValidateAttempt
          * Validates a login attempt (checks for brute-force attack)
          *
          * @param string $user User/E-Mail of login request
          * @param string $usrID Internal DB User-ID
          * @return bool Valid attempt (true) or brute-force attack (false)
          */
          private function ValidateAttempt($user, $usrID = -1)
          {
               if (isset($_COOKIE['mc_dc']) && $usrID != -1 && $usrID != null) {
                    // CHECK DEVICE COOKIE
                    $devicecookie = $_COOKIE['mc_dc'];
                    if ($this->ValidateDeviceCookie($usrID, $devicecookie)) {
                         if ($this->CheckDeviceCookie($usrID, $devicecookie)) {
                              return true;
                         } else {
                              return false;
                         }
                    }
               }
               // CHECK IF USER ALREADY IN DB
               if ($this->ValidateUnkownUser($user)) {
                    // CHECK IF LOCKED OR TOO MANY ATTEMPTS
                    if ($this->CheckUnknownUser($user)) {
                         return true;
                    } else {
                         return false;
                    }
               } else {
                    return true;
               }
               return false;
          }

          /**
          * AddAttempt
          * Adds an failed attempt in the db
          *
          * @param string $user User/E-Mail of login request
          * @param string $usrID Internal DB User-ID
          */
          private function AddAttempt($user, $usrID = -1) {
               if (isset($_COOKIE['mc_dc']) && $usrID != -1) {
                    $this->AddDeviceAttempt($usrID, $user);
                    return;
               }
               $this->AddUnknownAttempt($user);
          }

          /**
          * AddDeviceAttempt
          * Adds or increases an entry for the attempts in mc_devicecookies and locks all entries that are over $this->maxAttempts for $this->attemptTime minutes
          * If device cookie is invalid, it adds an entry via AddUnknownAttempt()
          *
          * @param string $usrID Internal DB User-ID
          * @param string $user User/E-Mail of login request
          * @return bool (irrelevant) Device cookie was valid (true), Device cookie was invalid (false)
          */
          private function AddDeviceAttempt($usrID, $user)
          {
               $devicecookie = $_COOKIE['mc_dc'];

               if ($this->ValidateDeviceCookie($usrID, $devicecookie)) {

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
               $this->AddUnknownAttempt($user);
               return false;
          }

          /**
          * AddUnknownAttempt
          * Adds or increases an entry for the attempts in mc_loginsfailed and locks all entries that are over $this->maxAttempts for $this->attemptTime minutes
          *
          * @param string $user User/E-Mail of login request
          */
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

          /**
          * ValidateUnkownUser
          * Checks if user is already in the mc_loginsfailed db
          *
          * @param string $user User/E-Mail of login request
          * @return bool User exists (true) or User does not already exists (false)
          */
          private function ValidateUnkownUser($user)
          {
               $stmt = $this->pdo->prepare("SELECT `fl_id` FROM `mc_loginsfailed` WHERE (`fl_user`=:user)");
               $stmt->bindParam(':user', strval($user));
               $stmt->execute();
               // CHECK IF USER IS IN DB
               $rowCount = $stmt->rowCount();
               if ($rowCount == 0) {
                    return false;
               }
               return true;
          }

          /**
          * CheckUnknownUser
          * Checks if user is currently deactivated because of a brute-force attack
          *
          * @param string $user User/E-Mail of login request
          * @return bool User is not locked [not in brute-force protection] (true) or user is locked (false)
          */
          private function CheckUnknownUser($user) {
               $stmt = $this->pdo->prepare("SELECT `fl_id` FROM `mc_loginsfailed` WHERE (`fl_user`=:user AND (`fl_locked_until` IS NULL))");
               $stmt->bindParam(':user', strval($user));
               $stmt->execute();

               // CHECK IF NOT LOCKED UNTIL
               $rowCount = $stmt->rowCount();
               if ($rowCount != 0) {
                    return true;
               }

               $stmt = $this->pdo->prepare("SELECT `fl_id` FROM `mc_loginsfailed` WHERE (`fl_user`=:user AND (`fl_locked_until` IS NULL OR `fl_locked_until` < CURRENT_TIMESTAMP))");
               $stmt->bindParam(':user', strval($user));
               $stmt->execute();

               // CHECK IF LOGIN EXISTS
               $rowCount = $stmt->rowCount();
               if ($rowCount == 0) {
                    // ACCOUNT IS LOCKED
                    return false;
               }
               // RESET ATTEMPTS
               $this->ResetUnknownAttempts($user);
               return true;
          }

          /**
          * ValidateDeviceCookie
          * Checks if device cookie of browser belongs to user id
          *
          * @param string $usrID Internal DB User-ID
          * @param string $devicecookie Device cookie supplied by user
          * @return bool Valid cookie in combination with user (true) or invalid cookie (false)
          */
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

          /**
          * CheckDeviceCookie
          * Checks if Device Cookie is currently deactivated because of a brute-force attack
          *
          * @param string $usrID Internal DB User-ID
          * @param string $devicecookie Device cookie supplied by user
          * @return bool Device Cookie is not locked [not in brute-force protection] (true) or Device Cookie is locked (false)
          */
          private function CheckDeviceCookie($usrID, $devicecookie)
          {
               $stmt = $this->pdo->prepare("SELECT `dc_id` FROM `mc_devicecookies` WHERE (`usr_id`=:usrid AND `dc_token`=:token AND (`dc_locked_until` IS NULL))");
               $stmt->bindParam(':usrid', strval($usrID));
               $stmt->bindParam(':token', strval($devicecookie));
               $stmt->execute();

               // CHECK IF NOT LOCKED UNTIL
               $rowCount = $stmt->rowCount();
               if ($rowCount != 0) {
                    return true;
               }

               $stmt = $this->pdo->prepare("SELECT `dc_id` FROM `mc_devicecookies` WHERE (`usr_id`=:usrid AND `dc_token`=:token AND (`dc_locked_until` IS NULL OR `dc_locked_until` < CURRENT_TIMESTAMP))");
               $stmt->bindParam(':usrid', strval($usrID));
               $stmt->bindParam(':token', strval($devicecookie));
               $stmt->execute();

               // CHECK IF LOGIN EXISTS
               $rowCount = $stmt->rowCount();
               if ($rowCount == 0) {
                    return false;
               }
               $this->ResetDeviceAttempts($usrID, $devicecookie);
               return true;
          }

          /**
          * ResetAttempts
          * Resets all attempts after a valid login for usrID, devicecookie and username/email
          *
          * @param string $user User / E-Mail
          * @param string $usrID Internal DB User-ID
          */
          private function ResetAttempts($user, $usrID) {
               $devicecookie = $_COOKIE['mc_dc'];
               if ($this->ValidateDeviceCookie($usrID, $devicecookie)) {
                    $this->IssueNewDeviceCookie($usrID, true);
                    return;
               }
               $this->ResetUnknownAttempts($user);
               $this->IssueNewDeviceCookie($usrID);
          }

          /**
          * ResetUnknownAttempts
          * Resets db mc_loginsfailed at username/email
          *
          * @param string $user User / E-Mail
          */
          private function ResetUnknownAttempts($user) {
               $stmt = $this->pdo->prepare("UPDATE `mc_loginsfailed` SET `fl_attempts`=0, `fl_locked_until`=NULL WHERE `fl_user`=:user");
               $stmt->bindParam(':user', strval($user));
               $stmt->execute();
          }

          /**
          * ResetDeviceAttempts
          * Resets the attempts made from a specific device cookie
          *
          * @param string $usrID Internal DB User-ID
          * @param string $devicecookie Device cookie supplied by user
          */
          private function ResetDeviceAttempts($usrID, $devicecookie) {
               $stmt = $this->pdo->prepare("UPDATE `mc_devicecookies` SET `dc_attempts`=0, `dc_locked_until`=NULL WHERE (`usr_id`=:usrid AND `dc_token`=:oldtoken)");

               $stmt->bindParam(':oldtoken', strval($devicecookie));
               $stmt->bindParam(':usrid', strval($usrID));
               $stmt->execute();
          }

          /**
          * IssueNewDeviceCookie
          * Issues a new device cookie (64bit) for a specific usrID and sets the cookie to the users browser
          *
          * @param string $usrID Internal DB User-ID
          * @param string $withcookie If set to true, the db will be updated instead of a new entry, because user logged in with cookie
          */
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
               $expires += 24 * 60 * 60 * 30 * 6; // 24h * 30d * 6 = 6 months
               CookieHandler::SetCookie("mc_dc", $devicecookie, true, intval($expires));
          }



     }
?>
