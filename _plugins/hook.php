<?php
function hook_add($name, $handler){
  global $sp;

  isset($sp['hook']['handlers'][$name]) || $sp['hook']['handlers'][$name] = [];

  $sp['hook']['handlers'][$name][] = $handler;
}

function hook_trigger($name, array $data = []){
  global $sp;

  if(isset($sp['hook']['handlers'][$name])){
    foreach($sp['hook']['handlers'][$name] as $handler){
      call_user_func($handler, $name, $data, $sp);
    }
  }

  if(isset($sp['hook']['location'])){
    foreach((array) $sp['hook']['location'] as $location){
      foreach(glob(sprintf('%s/%s.php', $location, $name)) as $handlerFile){
        include($handlerFile);
      }
    }
  }
}
