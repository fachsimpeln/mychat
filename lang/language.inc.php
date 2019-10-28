<?php

     /**
      * LanguageHandler
      * Provides multiple language support
      *
      * @author fachsimpeln
      * @category Language
      * @version language.system.db, v1.0, 2019-10-28
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
               $this->$langTable = json_decode(file_get_contents($pathLang . '/' . $lang . '.json'), true);
          }

          /**
          * GetString
          * Gets string in chosen language based on supplied id
          *
          * @param string $id ID of the string which should be printed
          * @return string String in chosen language
          */
          public function GetString($id)
          {
               return $langTable[$id];
          }
     }



?>
