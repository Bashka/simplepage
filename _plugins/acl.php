<?php
function acl_build(array $allowed){
  return function($role, $resource, $operation) use($allowed){
    if(isset($allowed[$role])){
      if($allowed[$role] == '*'){
        return true;
      }

      if(isset($allowed[$role][$resource])){
        if($allowed[$role][$resource] == '*'){
          return true;
        }

        return in_array($operation, $allowed[$role][$resource]);
      }
    }

    return false;
  };
}

function acl_control(array $allowed, $role = null, $resource = null, $operation = null){
  if(is_null($role)){
    $role = isset($_SESSION['role'])? $_SESSION['role'] : 'guest';
  }
  $resource = $resource?: $_SERVER['REQUEST_URI'];
  $operation = $operation?: $_SERVER['REQUEST_METHOD'];

  return call_user_func(acl_build($allowed), $role, $resource, $operation);
}

function acl_default_forbidder(){
  http_response_code(403);
  exit;
}

isset($sp['acl']['forbidder']) || $sp['acl']['forbidder'] = 'acl_default_forbidder';

if(isset($sp['acl']['allowed'])){
  if(!acl_control($sp['acl']['allowed'])){
    call_user_func($sp['acl']['forbidder']);
  }
}
