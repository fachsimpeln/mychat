<?php

     /**
      * LanguageHandler
      * Provides multiple language support
      *
      * @author fachsimpeln
      * @category Language
      * @version language.system.db, v2.0, 2019-12-29
      */
     class LanguageHandler
     {
          /** @var array|null Language Table read from file */
          private $langTable = null;

          /**
          * Constructor
          * Initialize LanguageHandler object
          *
          * @param string $lang Language of user (e.g. en-US)
          * @param string $pathLang Path to language files
          */
          function __construct($lang, $pathLang = './languages')
          {
               // Check if valid lang-code (format: en-US)
               $re = '/^[a-zA-Z]+-[a-zA-Z]+$/m';
               preg_match_all($re, $lang, $matches, PREG_SET_ORDER, 0);

               if (count($matches) > 0) {
                    $file = $pathLang . '/' . $lang . '.json';
               } else {
                    $file = $pathLang . '/en-us.json';
               }

               // Check if actually exists
               if (!file_exists($file)) {
                    $file = $pathLang . '/en-us.json';
               }

               // Set langTable to the json-decoded flattend array from the language file
               $this->$langTable = $this->array_flatten(json_decode(file_get_contents($file), true));
          }

          /**
          * GetString
          * Gets string in chosen language based on supplied id
          *
          * @param string $id ID of the string which should be printed (format: homepage-head-title)
          * @param array $replacements Replacements in string, e.g. {name}; comment out with \ (e.g. \{name\})
          * @return string String in chosen language
          */
          public function GetString($id, $replacements = array())
          {
               if (count($replacements) == 0) {
                    return $langTable[$id];
               }
               $string = $langTable[$id];
               foreach ($replacements as $key => $value) {
                    // Replace {name} with $value when not escaped
                    $key = preg_quote($key);
                    $curly_braces = '/(?=[^\\\\]){' . $key . '[^\\\\]?}/m';
                    $string = preg_replace($curly_braces, $value, $string);
               }

               // Remove double blackslash to only one (\\ -> \)
               $blackslash = '/(?:\\\\\\\\)/m';
               $subst = '\\\\';
               $string = preg_replace($blackslash, $subst, $string);

               // Remove \{ to { (\{name\} -> {name})
               $blackslash = '/(?:\\\\(?={)|\\\\(?=}))/m';
               $subst = '';
               $string = preg_replace($blackslash, $subst, $string);

               return $string;
          }

          /**
          * ArrayFlatten
          * Flattens an multidimensional array to a single dimensional array with a seperator (suffix) to the key
          *
          * @param array $array Array to be flattend
          * @param string $suffix Suffix to seperate the keys (-)
          * @param string $prefix Prefix to add to the keys
          * @return array Flattend single dimensional array
          */
          private function array_flatten($array, $suffix = '-', $prefix = '') {
              $result = array();
              foreach ($array as $key => $value) {
                  if (is_array($value)) {
                      $result = $result + array_flatten($value, $suffix, $prefix . $key . $suffix);
                  }
                  else {
                      $result[$prefix . $key] = $value;
                  }
              }
              return $result;
          }
     }



?>
