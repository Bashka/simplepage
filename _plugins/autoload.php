<?php
spl_autoload_register(function($class) use($sp){
  isset($sp['autoload']) || $sp['autoload'] = __DIR__ . '/../_autoload';
  foreach((array) $sp['autoload'] as $dir){
    $path = sprintf('%s/%s.php', $dir, str_replace('\\', '/', $class));
    if(is_readable($path)){
      include($path);
    }
  }
});
