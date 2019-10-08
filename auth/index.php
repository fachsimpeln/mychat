<?php

     // INITIALIZE
     require '../inc/db.inc.php';
     $pdo = DatabaseConnection::connect();

     // GET USER AUTH
     $user = $_POST['user'];
     $password = $_POST['password'];

     // GENERATE RANDOM TOKEN (user-identifier enc-id, user-token 512bit random)
     $crypto = true;
     $usertoken = bin2hex(openssl_random_pseudo_bytes(512, $crypto));


     // CHECK IF ALREADY IN DATABASE AS A ID
     $bitlength = 32;
     $iD = true; // in Database
     while ($iD) {
          $iD = false;

          $useridentifier = bin2hex(openssl_random_pseudo_bytes($bitlength, $crypto));

          // CHECK DATABASE AND SET $iD
          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $stmt = $pdo->prepare("SELECT * FROM `mc_logins` WHERE `login_userIdentifier`=:userID");
          $stmt->bindParam(':userID', strval($useridentifier));
          $stmt->execute();

          $rowCount = $stmt->rowCount();
          if ($rowCount > 0) {
               // User-Identifier already in database
               $iD = true;
               $bitlength += 1;
          }
     }

     // SAVE IN DATABASE (table mc_logins)

     // OUTPUT TO USER

?>
