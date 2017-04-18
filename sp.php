<?php
$sp = isset($sp)? $sp : [];
if(is_readable(__DIR__ . '/config.php')){
  $sp = array_replace_recursive(include(__DIR__ . '/config.php'), $sp);
}

isset($sp['end']) ||
$sp['end'] = function($content){
  return $content;
};

if(isset($sp['plugins'])){
  foreach($sp['plugins'] as $plugin){
    include($plugin);
  }
}

ob_start();
register_shutdown_function(function() use($sp){
  echo call_user_func($sp['end'], ob_get_clean());
});
