<?php
     /**
      * Database-Management
      */
     class DatabaseConnection
     {

          public static $mc_pdo = null;

          public static function connect()
          {
               // DEFINE PARAMETERS
               $db_host = '';
               $db_dbname = '';
               $db_user = '';
               $db_pass = '';

               // INITIALIZE PDO (UTF-8)
               try {
                    self::$mc_pdo = new PDO('mysql:host=' . $db_host . ';dbname=' . $db_dbname . ';charset=utf8', $db_user, $db_pass);
               } catch (\Exception $e) {
                    // ERROR CREATING DATABASE CONNECTION
                    die('no_connection');
               }
          }
     }

?>
