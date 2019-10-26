<?php
     // Include setup.inc.
     require 'inc/setup.inc.php';

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
     <head>
          <meta charset="utf-8">
          <title>mychat</title>

          <!-- Include favicon -->
          <link rel="icon" type="image/svg+xml" href="./img/mychat_logo.svg" sizes="any">
     </head>

     <body>
          <h1>mychat</h1>
          <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
     </body>

     <h2>Send message - Testing</h2>
     <form class="" action="./messages/send/" method="post">
          <input type="text" name="receiver" value="" placeholder="Receiver Username...">
          <br /><br />
          <textarea name="message" rows="8" cols="80" placeholder="Message text..."></textarea>
          <br /><br />
          <input type="text" name="rtype" value="private"><br /><br />
          <input type="submit" name="" value="Send Message!">
     </form>
</html>
