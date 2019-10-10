<?php

     /**
      * Permission Handler
      */
     class PermissionHandler
     {

          private $pdo = null;

          private $permissions = array();

          function __construct($pdo)
          {
               $this->pdo = $pdo;
          }

          public function GetUserPermission($usrID)
          {
               return $this->permissions;
          }

          public function SetUserPermission($usrID, $permission)
          {

          }

     }


?>
