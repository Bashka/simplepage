<?php
function pdo_connect(array $config){
  static $pdo = null;

  if(is_null($pdo)){
    $dsn = '';
    if(isset($config['dsn'])){
      if(is_array($config['dsn'])){
        $dsn = sprintf(
          '%s:%s',
          array_shift($config['dsn']),
          implode(';', array_map(function($option, $value){
            return $option . '=' . $value;
          }, array_keys($config['dsn']), $config['dsn']))
        );
      }
      else{
        $dsn = $config['dsn'];
      }
    }
    isset($config['username']) || $config['username'] = '';
    isset($config['password']) || $config['password'] = '';
    isset($config['options']) || $config['options'] = [];
    
    $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
  }

  return $pdo;
}

function pdo_statement_build(PDO $pdo, $sql, array $config = []){
  $statement = $pdo->prepare($sql);
  $prototype = isset($config['prototype'])? $config['prototype'] : 'stdClass';
  $statement->setFetchMode(PDO::FETCH_CLASS, $prototype);
  if(isset($config['params'])){
    foreach($config['params'] as $name => $variable){
      if(is_array($variable)){
        $type = $variable['type'];
        $variable = $variable['value'];
      }
      else{
        switch(gettype($variable)){
          case 'NULL':
            $type = PDO::PARAM_NULL;
            break;
          case 'boolean':
            $type = PDO::PARAM_BOOL;
            break;
          case 'integer':
          case 'double':
            $type = PDO::PARAM_INT;
            break;
          default:
            $type = PDO::PARAM_STR;
        }
      }
      $statement->bindValue($name, $variable, $type);
    }
  }

  return $statement;
}

if(isset($sp['pdo']['queries'])){
  foreach($sp['pdo']['queries'] as $container => $config){
    $statement = pdo_statement_build(pdo_connect($sp['pdo']), $config[0], $config);
    if(!isset($config['lazy'])){
      $statement->execute();
    }
    if(is_string($container)){
      ${$container} = $statement;
    }
  }
}
