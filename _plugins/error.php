<?php
function error_default_handler($error, $content, array $sp){
  return var_export($error, true);
}

isset($sp['error']['handler']) || $sp['error']['handler'] = 'error_default_handler';

middleware_add(function($content, array $next) use($sp){
  $error = error_get_last();
  if(is_array($error)){
    return call_user_func($sp['error']['handler'], $error, $content, $sp);
  }

  return middleware_next($content, $next);
});
