<?php

     /**
     * CookieHandler
     * Handles setting and reading cookie data
     *
     * @author fachsimpeln
     * @category Cookie System
     * @version cookie.system.db, v1.0, 2019-12-29
     */
     class CookieHandler
     {
          /**
          * SetCookie
          * Sets a cookie to the user's browser
          *
          * @param string $key Key for the cookie (e.g. mc_sid)
          * @param string $value Value for the cookie (e.g. 9291)
          * @param bool $secure Only over HTTPS and httpOnly (Standard: true)
          * @param int $expires Cookie expiration as UNIX timestamp
          */
          public static function SetCookie($key, $value, $secure = true, $expires = 0) {
               // Standard 2 Days
               if ($expires === 0) {
                    $expires = time() + (48 * 60 * 60); // 48h * 60min * 60 sec
               }
               if ($secure) {
                    setcookie($key, $value, intval($expires), '/', $_SERVER['HTTP_HOST'], true, true);
                    return;
               } else {
                    setcookie($key, $value, intval($expires), '/', $_SERVER['HTTP_HOST']);
               }
          }

          /**
          * GetCookie
          * Gets cookie value from key
          *
          * @param string $key Key for the cookie (e.g. mc_sid)
          * @return string Value of the cookie
          */
          public static function GetCookie($key) {
               return $_COOKIE[$key];
          }

          /**
          * RemoveCookie
          * Removes a cookie
          *
          * @param string $key Key for the cookie (e.g. mc_sid)
          */
          public static function RemoveCookie($key) {
               setcookie($key, "", time() - 3600);
          }
     }


?>
