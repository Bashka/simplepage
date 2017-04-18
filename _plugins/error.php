<?php
function error_default_handler($error, $content, array $sp){
  return var_export($error, true);
}

middleware_add(function($content, array $next) use($sp){
  $error = error_get_last();
  if(is_array($error)){
    if(!isset($sp['error'])){
      $sp['error'] = 'error_default_handler';
    }

    return call_user_func($sp['error'], $error, $content, $sp);
  }

  return middleware_next($content, $next);
});
