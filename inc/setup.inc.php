<?php
     ini_set('session.use_strict_mode', 'On');
     ini_set('session.cookie_httponly', 'On');
     ini_set('session.sid_length', '128');
     ini_set('session.hash_function', 'sha512');


     session_name('sid_mychat');
     session_start();
?>
