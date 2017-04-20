<?php
isset($sp['autoload']['map']) || $sp['autoload']['map'] = [];
isset($sp['autoload']['psr4']) || $sp['autoload']['psr4'] = __DIR__ . '/../_autoload';

spl_autoload_register(function($class) use($sp){
  if(isset($sp['autoload']['map'][$class])){
    include($sp['autoload']['map'][$class]);
  }
  else{
    foreach((array) $sp['autoload']['psr4'] as $dir){
      $path = sprintf('%s/%s.php', $dir, str_replace('\\', '/', $class));
      if(is_readable($path)){
        include($path);
        break;
      }
    }
  }
});
