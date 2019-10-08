<?php
     // GET USER AUTH
     $user = $_POST['user'];
     $password = $_POST['password'];

     // GENERATE RANDOM TOKEN (user-identifier enc-id, user-token 512bit random)
     $crypto = true;
     $usertoken = openssl_random_pseudo_bytes(512, $crypto);


     // CHECK IF ALREADY IN DATABASE AS A ID
     $bitlength = 32;
     $iD = true; // in Database
     while ($iD) {
          $useridentifier = openssl_random_pseudo_bytes($bitlength, $crypto);

          // CHECK DATABASE AND SET $iD

          $iD = false;
     }

     // SAVE IN DATABASE (table mc_logins)

     // OUTPUT TO USER

?>
