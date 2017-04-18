<?php
function input_resolve($type){
  return [
    INPUT_GET => $_GET,
    INPUT_POST => $_POST,
    INPUT_COOKIE => $_COOKIE,
    INPUT_SERVER => $_SERVER,
    INPUT_ENV => $_ENV,
  ][$type];
}

function input_filter_build($config, array $customFilters = []){
  $filter = FILTER_CALLBACK;
  $options = [];
  $flags = null;
  $comment = '';

  if(is_callable($config)){
    $options = $config;
  }
  elseif(is_string($config)){
    $options = $customFilters[$config];
  }
  elseif(is_array($config)){
    if(is_string($config['filter'])){
      $options = $customFilters[$config['filter']];
    }
    else{
      $filter = $config['filter'];
      $options = isset($config['options'])? $config['options'] : [];
    }
    $flags = isset($config['flags'])? $config['flags'] : null;
    $comment = isset($config['comment'])? $config['comment'] : '';
  }
  else{
    $filter = $config;
  }

  return [$filter, $options, $flags, $comment];
}

function input_invalid_register($name, $comment = ''){
  static $invalid = [];

  if(!isset($invalid[$name])){
    $invalid[$name] = [];
  }
  $invalid[$name][] = $comment;

  return $invalid;
}

function input_default_shredder(array $invalid){
  http_response_code(400);
  exit;
}

isset($sp['input']['shredder']) || $sp['input']['shredder'] = 'input_default_shredder';
isset($sp['input']['filters']) || $sp['input']['filters'] = [];

if(isset($sp['input'])){
  $sp['input']['invalid'] = [];
  foreach($sp['input'] as $type => $definition){
    if(!is_int($type)){
      continue;
    }
    $data = input_resolve($type);
    foreach($definition as $var => $filters){
      foreach($filters as $config){
        list(
          $filter,
          $options,
          $flags,
          $comment
        ) = input_filter_build($config, $sp['input']['filters']);

        $data[$var] = filter_var($data[$var], $filter, [
          'options' => $options,
          'flags' => $flags,
        ]);

        if($data[$var] === false){
          $sp['input']['invalid'] = input_invalid_register($var, $comment);
        }
      }
    }
  }

  if(!empty($sp['input']['invalid'])){
    call_user_func($sp['input']['shredder'], $sp['input']['invalid']);
  }
}
