<?php
     /**
     * PreventXSS
     * Prevent XSS attacks by replacing all html tag with the special chars
     * ! Always use when output to user !
     *
     * @param string $text Text to be filtered
     * @return string Filtered text
     */
     function PreventXSS($text)
     {
          return htmlspecialchars($text, ENT_QUOTES);
     }
?>
