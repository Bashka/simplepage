<?php
function i18n_get_locale($category){
  $locale = setlocale($category, 0);
  $encoding = '';
  if(strpos($locale, '.') !== false){
    list($locale, $encoding) = explode('.', $locale);
  }

  return [$locale, $encoding];
}

function i18n_load_translate($domain, $locale = null){
  if(is_null($locale)){
    $locale = i18n_get_locale(LC_CTYPE)[0];
  }

  $path = sprintf('%s/%s.php', $domain, $locale);
  if(!file_exists($path)){
    return [];
  }

  return include($path);
}

function i18n($string, $domain = null, $locale = null){
  global $sp;

  if(is_null($domain)){
    $domain = $sp['i18n']['domain'];
  }

  $translate = i18n_load_translate($domain, $locale);
  if(isset($translate[$string])){
    $string = $translate[$string];
  }

  return $string;
}

function i18n_plural($number, $plural, $domain = null, $locale = null){
  global $sp;

  if(is_null($domain)){
    $domain = $sp['i18n']['domain'];
  }

  $translate = i18n_load_translate($domain, $locale);
  if(isset($translate[$plural])){
    $plural = $translate[$plural][
      call_user_func($translate['']['plural_forms'], $number)
    ];
  }

  return sprintf($plural, $number);
}

isset($sp['i18n']) || $sp['i18n'] = [
  'domain' => '_locale',
];
