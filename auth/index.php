<?php

     // INITIALIZE
     require '../inc/db.inc.php';
     require '../inc/login.inc.php';
     require '../inc/security/xss.inc.php';
     $pdo = DatabaseConnection::connect();

     // GET USER AUTH
     $email = $_POST['email'];
     $password = $_POST['password'];

     // CHECK USER AUTH
     $loginHandler = new LoginHandler();
     if (!$loginHandler->LoginUser($email, $password)) {
          // NOT AUTHENTICATED
          die('no_auth');
     }
     // GET USR-ID
     $usrID = $loginHandler->usrID;

     // GENERATE RANDOM TOKEN (user-identifier enc-id, user-token 512bit random)
     $crypto = true;
     $logintoken = bin2hex(openssl_random_pseudo_bytes(512, $crypto));


     // CHECK IF ALREADY IN DATABASE AS A ID
     $bitlength = 32;
     $iD = true; // in Database
     while ($iD) {
          $iD = false;

          $loginIdentifier = bin2hex(openssl_random_pseudo_bytes($bitlength, $crypto));

          // CHECK DATABASE AND SET $iD
          $stmt = $pdo->prepare("SELECT * FROM `mc_logins` WHERE `login_userIdentifier`=:userID");
          $stmt->bindParam(':userID', strval($loginIdentifier));
          $stmt->execute();

          $rowCount = $stmt->rowCount();
          if ($rowCount > 0) {
               // User-Identifier already in database
               $iD = true;
               $bitlength += 1;
          }
     }

     // SET EXPIRE TO 24h
     $expires = time();
     $expires += 24 * 60 * 60;

     // SAVE IN DATABASE (table mc_logins)
     $stmt = $pdo->prepare("INSERT INTO `mc_logins` (`login_userIdentifier`, `login_token`, `login_expires`, `usr_id`) VALUES (:loginIdentifier, :token, :expires, :usrID)");
     $stmt->bindParam(':loginIdentifier', strval($loginIdentifier));
     $stmt->bindParam(':token', strval($logintoken));
     $stmt->bindParam(':expires', strval($expires));
     $stmt->bindParam(':usrID', strval($usrID));
     $stmt->execute();

     // OUTPUT TO USER
     die(PreventXSS($loginIdentifier) . '|' . PreventXSS($logintoken));
?>
