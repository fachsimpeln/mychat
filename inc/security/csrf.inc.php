<?php

     /**
     * CRSF Protection
     * Checks all requests for a valid CRSF Token and issues them
     * requires CookieHandler
     *
     * @author fachsimpeln
     * @category Security System
     * @version security.system.db, v1.0, 2019-12-29
     */
     class CRSF
     {
          /**
          * GenerateCRSF
          * Generates a new CRSF Token and sets to the users browser
          */
          public function GenerateCRSF()
          {
               $token = base64_encode( openssl_random_pseudo_bytes(32));
               CookieHandler::SetCookie("mc_crsf", $token);
          }

          /**
          * GetFormInput
          * Return a valid input field with the crsf token
          *
          * @param bool $print Should the input be printed rightaway? (Standard: false)
          * @return string|null Vaild hidden html form input field
          */
          public function GetFormInput($print = false)
          {
               $crsf = $this->GetCRSFValue();
               $form_field = '<input type="hidden" name="'. $crsf[0] . '" value="'. $crsf[1] . '">';
               if ($print) {
                    print $form_field;
                    return null;
               }
               return $form_field;
          }

          /**
          * GetCRSFValue
          * Return a valid token_id and token
          *
          * @return array {token_id, token} for sending requests
          */
          public function GetCRSFValue()
          {
               $token = CookieHandler::GetCookie("mc_crsf");
               $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
               $token_id = substr(str_shuffle($permitted_chars), 0, 16);
               $calc_token = hmac_hash('sha256', $token_id, $token);
               $ar = array();
               $ar[0] = 'crsf_token_' . $token_id;
               $ar[1] = $calc_token;
               return $ar;
          }

          /**
          * CheckCRSF
          * Checks if the supplied crsf token is valid
          *
          * @param array $request Request (POST/GET) as array
          * @return bool CRSF token valid (true) or invalid (false)
          */
          public function CheckCRSF($request)
          {
               $token_id = null;
               $sub_token = null;
               foreach ($request as $key => $value) {
                    if ($this->startsWith($key, 'crsf_token_')) {
                         $token_id = explode('_', $key)[2];
                         $sub_token = $value;
                    }
               }
               $calc_token = hmac_hash('sha256', $token_id, CookieHandler::GetCookie("mc_crsf"));

               if ($calc_token === $sub_token) {
                    return true;
               } else {
                    return false;
               }
          }

          /**
          * startsWith
          * Checks if the supplied haystack begins with the needle
          *
          * @param string $haystack Text that should begin with $needle
          * @param string $needle Starting text snippet
          * @return bool Starts with needle (true) or starts not with needle (false)
          */
          private function startsWith($haystack, $needle)
          {
               $length = strlen($needle);
               return (substr($haystack, 0, $length) === $needle);
          }
     }


?>
