<?php
middleware_add(function($content, array $next) use($sp){
  if(isset($sp['layout']['layout'])){
    extract($sp['layout']);
    ob_start();
    include($sp['layout']['layout']);
    $content = ob_get_clean();
  }

  return middleware_next($content, $next);
});
