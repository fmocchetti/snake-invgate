<?php

function translate($move) {
  switch ($move) {
    //left
    case 0:
      return array("x" => -1, "y" => 0);
    //up
    case 1:
      return array("x" => 0, "y" => 1);
    //down      
    case 2:
      return array("x" => 0, "y" => -1);
    //right
    case 3:
      return array("x" => 1, "y" => 0);      
    //don't move
    default:
      return array("x" => 0, "y" => 0);
  }
}

function arrayMergeNumericValues() {
  $arrays = func_get_args();
  $merged = array();
  foreach ($arrays as $array)
  {
      foreach ($array as $key => $value)
      {
          if ( ! is_numeric($value))
          {
              continue;
          }
          if ( ! isset($merged[$key]))
          {
              $merged[$key] = $value;
          }
          else
          {
              $merged[$key] += $value;
          }
      }
  }
  return $merged;
}

function currentPosition($you) {
  return $you->head;
}


function outOfBoundaries($data, $move) {
  error_log('Out of Boundaries check');
  $possibleMove = [ 'left', 'up', 'down', 'right'];
  $currPosition = currentPosition($data->you);
  error_log('CurrentPosition: '.print_r($currPosition, true));
  $nextMove = translate($move);
  error_log('Next Move: '.print_r($nextMove, true));
  $nextPosition = arrayMergeNumericValues($currPosition,$nextMove);  
  error_log('Next Position: '.print_r($nextPosition, true));
  if($nextPosition["x"] >= $data->board->width || 
     $nextPosition["x"] < 0 || 
     $nextPosition["y"] >= $data->board->height ||
     $nextPosition["y"] < 0  
     )
    return true;

  return false;
}

function isMyBody($body, $nextPosition) {
  foreach($body as $bodyPart) {
    error_log('Body Part: '.print_r($bodyPart, true));  
    error_log('Next Position: '.print_r((object)$nextPosition, true));
  
    if($bodyPart == (object)$nextPosition)  
      return true;
  }
  return false;
}

function thereIsAnotherSnake($snakes, $nextPosition) {
  foreach($snakes as $snake) {
    foreach($snake->body as $bodyPart) {
      error_log('Snake Body Part: '.print_r($bodyPart, true));  
      error_log('Next Position: '.print_r((object)$nextPosition, true));
    
      if($bodyPart == (object)$nextPosition)  
        return true;
    }
  }
  return false;
}

function collide($data, $move) {
  error_log('Collide check');
  $possibleMove = [ 'left', 'up', 'down', 'right'];
  $currPosition = currentPosition($data->you);
  error_log('CurrentPosition: '.print_r($currPosition, true));
  $nextMove = translate($move);
  error_log('Next Move: '.print_r($nextMove, true));
  $nextPosition = arrayMergeNumericValues($currPosition,$nextMove);  
  if(isMyBody($data->you->body, $nextPosition) || 
     thereIsAnotherSnake($data->board->snakes, $nextPosition))
    return true;

  return false;

}

function detectLastDirection($you) {
   $body = $you->body;

   if($body[0]->x == $body[1]->x) {
     if($body[0]->y == ($body[1]->y + 1)) {
       return 1;
     } else {
       return 2;
     }
   } else {
     if($body[0]->x == ($body[1]->x + 1)) {
       return 3;
     } else {
       return 0;
     }
   }
}

function moreNearFood($foods,$you) {
  $sortedFood = [];
  $head = $you->head;
  $x = null;
  $y = null;
  $minValue = 9999;
  $nearFood = [];
  foreach($foods as $food) {
    error_log('Food: '.print_r($food, true));
    error_log('Head: '.print_r($head, true));
    $x = $food->x - $head->x;
    $y = $food->y - $head->y;
    $aux = sqrt(pow($x,2)+pow($y,2));
    if($aux < $minValue) {
      $minValue = $aux;
      $nearFood = $food;
    }    
  }
  return $nearFood;
}

function findFood($foods,$you) {
  $food = moreNearFood($foods,$you);
  $currPosition = currentPosition($you);
  $deltaX = $food->x - $currPosition->x;
  $deltaY = $food->y - $currPosition->y;
  if(abs($deltaX) >= abs($deltaY)) {
    if($deltaX > 0) 
      return 3;  
    return 0;
  } else {
    if($deltaY > 0) 
      return 1;
    return 2;     
  }
}

function move($data) {
  $nextDirection = 0;
  $you = $data->you;
  if($data->you->length > 1) {
    $nextDirection = detectLastDirection($you);
  } 

  if(count($food = $data->board->food)) {
    $nextDirection = findFood($food,$you);
  }

  return nextMove($data,$nextDirection,5);
}

//[ 'left', 'up', 'down', 'right']
function nextMove($data,$nextMove,$tries) {
  $tries--;
  if($tries == 0)
    throw new \Exception('Can\'t move on any direction');

  $possibleMove = [ 'left', 'up', 'down', 'right'];
  error_log('Checking: '.$possibleMove[$nextMove]);
  if(outOfBoundaries($data,$nextMove) || collide($data,$nextMove)) {
    $nextMove = ++$nextMove % 4;
    $nextMove = nextMove($data,$nextMove,$tries);
  } else {
    error_log('Return move: '.$possibleMove[$nextMove]);
    return $nextMove;
  }
  error_log('Return move: '.$possibleMove[$nextMove]);

  return $nextMove;
}
