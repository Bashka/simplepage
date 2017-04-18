<?php
function middleware_next($content, array $middleware){
  return call_user_func(array_shift($middleware), $content, $middleware);
}

function middleware_add($middleware){
  static $queue = [];

  array_push($queue, $middleware);

  return $queue;
}

$sp['end'] = function($content) use($sp){
  return middleware_next($content, middleware_add($sp['end']));
};
