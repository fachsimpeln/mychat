<?php
     function PreventXSS($text)
     {
          return htmlspecialchars($text, ENT_QUOTES);
     }

?>
