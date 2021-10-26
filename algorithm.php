<?php

const MOVE_LEFT = 0;
const MOVE_UP = 1;
const MOVE_DOWN = 2;
const MOVE_RIGHT = 3;

function translate($move) {
  switch ($move) {
    //left
    case MOVE_LEFT:
      return array("x" => -1, "y" => 0);
    //up
    case MOVE_UP:
      return array("x" => 0, "y" => 1);
    //down
    case MOVE_DOWN:
      return array("x" => 0, "y" => -1);
    //right
    case MOVE_RIGHT:
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
  $possibleMove = [ 'left', 'up', 'down', 'right'];
  $currPosition = currentPosition($data->you);
  $nextMove = translate($move);
  $nextPosition = arrayMergeNumericValues($currPosition,$nextMove);
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
    if($bodyPart == (object)$nextPosition)
      return true;
  }
  return false;
}

function thereIsAnotherSnake($snakes, $nextPosition) {
  foreach($snakes as $snake) {
    foreach($snake->body as $bodyPart) {
      if($bodyPart == (object)$nextPosition)
        return true;
    }
  }
  return false;
}

function collide($data, $move, $tries) {
  //error_log('Collide check');
  $possibleMove = [ 'left', 'up', 'down', 'right'];
  $currPosition = currentPosition($data->you);
  //error_log('CurrentPosition: '.print_r($currPosition, true));
  $nextMove = translate($move);
  //error_log('Next Move: '.print_r($nextMove, true));
  $nextPosition = arrayMergeNumericValues($currPosition,$nextMove);
  if(isMyBody($data->you->body, $nextPosition) ||
     thereIsAnotherSnake($data->board->snakes, $nextPosition)||
      ($tries > 5 && impasse($data, (object) $nextPosition, $move, $currPosition)) ||
      ($tries > 9 && conflict($data, (object) $nextPosition, $move, $currPosition))
     )
    return true;

  return false;

}

function anObstacle($position, $data) {
  foreach($data->board->snakes as $snake) {
    foreach($snake->body as $bodyPart) {
      if($bodyPart == (object)$position)
        return true;
    }
  }

  foreach($data->you->body as $bodyPart) {
    if($bodyPart == (object)$position)
      return true;
  }


  if($position->x >= $data->board->width ||
     $position->x < 0 ||
     $position->y >= $data->board->height ||
     $position->y < 0
  ) {
    return true;
  }

  return false;
}

function flattenObstacles($data) {
    $obstacles = [];
    foreach($data->board->snakes as $snake) {
        foreach($snake->body as $bodyPart) {
            $obstacles[] = $bodyPart;
        }
    }

    foreach($data->you->body as $bodyPart) {
        $obstacles[] = $bodyPart;
    }

    return $obstacles;
}

function somethingInPath($data, $currPosition, $nextDirection) {
    $obstacles = flattenObstacles($data);
    $x = $currPosition->x;
    $y = $currPosition->y;
    switch($nextDirection) {
        case MOVE_LEFT:
            foreach($obstacles as $obstacle) {
                if(($obstacle->x < $x && $obstacle->x >= $x - 2) && $obstacle->y == $y ) {
                    return true;
                }
            }
            break;
        case MOVE_RIGHT:
            foreach($obstacles as $obstacle) {
                if(($obstacle->x > $x && $obstacle->x <= $x + 2) && $obstacle->y == $y ) {
                    return true;
                }
            }
            break;
        case MOVE_DOWN:
            foreach($obstacles as $obstacle) {
                if(($obstacle->y < $y && $obstacle->y >= $y - 2) && $obstacle->x == $x ) {
                    return true;
                }
            }
            break;
        case MOVE_UP:
            foreach($obstacles as $obstacle) {
                if(($obstacle->y > $y && $obstacle->y <= $y + 2) && $obstacle->x == $x ) {
                    return true;
                }
            }
            break;
        default:
            break;
    }
    return false;
}

function impasse($data, $currPosition, $nextDirection, $prevPosition) {
  error_log('CurrentPosition: '.print_r($currPosition, true));
  switch ($nextDirection) {
    //left
    case MOVE_LEFT:
        error_log('Left A: '.print_r((object)(array( 'x' => $prevPosition->x + (-1) , 'y' => $prevPosition->y + 1)), true));
        error_log('Left B: '.print_r((object)(array( 'x' => $prevPosition->x + (-1) , 'y' => $prevPosition->y + (-1))), true));
        if((anObstacle((object)(array( 'x' => $prevPosition->x + (-1) , 'y' => $prevPosition->y + 1)),$data) &&
           anObstacle((object)(array( 'x' => $prevPosition->x + (-1) , 'y' => $prevPosition->y + (-1))),$data) &&
            (somethingInPath($data, $prevPosition, $nextDirection) || outOfBoundaries($data, MOVE_LEFT)))
            )
           return true;
      break;
    //up
    case MOVE_UP:
        error_log('UP A: '.print_r((object)(array( 'x' => $prevPosition->x + 1 , 'y' => $prevPosition->y + 1)), true));
        error_log('UP B: '.print_r((object)(array( 'x' => $currPosition->x + (-1) , 'y' => $prevPosition->y + 1)), true));
        if(anObstacle((object)(array( 'x' => $prevPosition->x + 1 , 'y' => $prevPosition->y + 1)),$data) &&
          anObstacle((object)(array( 'x' => $prevPosition->x + (-1) , 'y' => $prevPosition->y + 1)),$data) &&
            (somethingInPath($data, $prevPosition, $nextDirection) || outOfBoundaries($data, MOVE_UP)))
          return true;
      break;
    //down
    case MOVE_DOWN:
        error_log('UP B: '.print_r((object)(array( 'x' => $prevPosition->x + 1 , 'y' => $prevPosition->y + (-1))), true));
        error_log('DOWN B: '.print_r((object)(array( 'x' => $prevPosition->x + (-1) , 'y' => $prevPosition->y + (-1))), true));
        if(anObstacle((object)(array( 'x' => $prevPosition->x + 1 , 'y' => $prevPosition->y + (-1))),$data) &&
            anObstacle((object)(array( 'x' => $prevPosition->x + (-1) , 'y' => $prevPosition->y + (-1))),$data) &&
            (somethingInPath($data, $prevPosition, $nextDirection) || outOfBoundaries($data, MOVE_DOWN)))
          return true;
      break;
    //right
    case MOVE_RIGHT:
        error_log('RIGHT A: '.print_r((object)(array( 'x' => $prevPosition->x + 1 , 'y' => $prevPosition->y + 1)), true));
        error_log('RIGHT B: '.print_r((object)(array( 'x' => $prevPosition->x + 1 , 'y' => $prevPosition->y + (-1))), true));
        if(anObstacle((object)(array( 'x' => $prevPosition->x + 1 , 'y' => $prevPosition->y + 1)),$data) &&
            anObstacle((object)(array( 'x' => $prevPosition->x + 1 , 'y' => $prevPosition->y + (-1))),$data) &&
            (somethingInPath($data, $prevPosition, $nextDirection) || outOfBoundaries($data, MOVE_RIGHT)))
          return true;
      break;
    default:
      break;
  }
  return false;
}

function conflict($data, $currPosition, $nextDirection, $prevPosition) {
    error_log('CurrentPosition: '.print_r($currPosition, true));
    switch ($nextDirection) {
        //left
        case MOVE_LEFT:
            error_log('Left A: '.print_r((object)(array( 'x' => $prevPosition->x + (-1) , 'y' => $prevPosition->y + 1)), true));
            error_log('Left B: '.print_r((object)(array( 'x' => $prevPosition->x + (-1) , 'y' => $prevPosition->y + (-1))), true));
            if(((anObstacle((object)(array( 'x' => $prevPosition->x + (-1) , 'y' => $prevPosition->y + 1)),$data) ||
                anObstacle((object)(array( 'x' => $prevPosition->x + (-1) , 'y' => $prevPosition->y + (-1))),$data)) &&
                (somethingInPath($data, $prevPosition, $nextDirection) || outOfBoundaries($data, MOVE_LEFT)))
            )
                return true;
            break;
        //up
        case MOVE_UP:
            error_log('UP A: '.print_r((object)(array( 'x' => $prevPosition->x + 1 , 'y' => $prevPosition->y + 1)), true));
            error_log('UP B: '.print_r((object)(array( 'x' => $currPosition->x + (-1) , 'y' => $prevPosition->y + 1)), true));
            if((anObstacle((object)(array( 'x' => $prevPosition->x + 1 , 'y' => $prevPosition->y + 1)),$data) ||
                anObstacle((object)(array( 'x' => $prevPosition->x + (-1) , 'y' => $prevPosition->y + 1)),$data)) &&
                (somethingInPath($data, $prevPosition, $nextDirection)  || outOfBoundaries($data, MOVE_UP)))
                return true;
            break;
        //down
        case MOVE_DOWN:
            error_log('UP B: '.print_r((object)(array( 'x' => $prevPosition->x + 1 , 'y' => $prevPosition->y + (-1))), true));
            error_log('DOWN B: '.print_r((object)(array( 'x' => $prevPosition->x + (-1) , 'y' => $prevPosition->y + (-1))), true));
            if((anObstacle((object)(array( 'x' => $prevPosition->x + 1 , 'y' => $prevPosition->y + (-1))),$data) ||
                anObstacle((object)(array( 'x' => $prevPosition->x + (-1) , 'y' => $prevPosition->y + (-1))),$data)) &&
                (somethingInPath($data, $prevPosition, $nextDirection)  || outOfBoundaries($data, MOVE_DOWN)))
                return true;
            break;
        //right
        case MOVE_RIGHT:
            error_log('RIGHT A: '.print_r((object)(array( 'x' => $prevPosition->x + 1 , 'y' => $prevPosition->y + 1)), true));
            error_log('RIGHT B: '.print_r((object)(array( 'x' => $prevPosition->x + 1 , 'y' => $prevPosition->y + (-1))), true));
            if((anObstacle((object)(array( 'x' => $prevPosition->x + 1 , 'y' => $prevPosition->y + 1)),$data) ||
                anObstacle((object)(array( 'x' => $prevPosition->x + 1 , 'y' => $prevPosition->y + (-1))),$data)) &&
                (somethingInPath($data, $prevPosition, $nextDirection)  || outOfBoundaries($data, MOVE_RIGHT)))
                return true;
            break;
        default:
            break;
    }
    return false;
}

function detectLastDirection($you) {
   $body = $you->body;

   if($body[0]->x == $body[1]->x) {
     if($body[0]->y == ($body[1]->y + 1)) {
       return MOVE_UP;
     } else {
       return MOVE_DOWN;
     }
   } else {
     if($body[0]->x == ($body[1]->x + 1)) {
       return MOVE_RIGHT;
     } else {
       return MOVE_LEFT;
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
    //error_log('Food: '.print_r($food, true));
    //error_log('Head: '.print_r($head, true));
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
      return MOVE_RIGHT;
    return MOVE_LEFT;
  } else {
    if($deltaY > 0)
      return MOVE_UP;
    return MOVE_DOWN;
  }
}

function goingToCrashWithSnake($data,$move) {
    //error_log('Collide check');
    if(!count($data->board->snakes))
        return false;
    $currPosition = currentPosition($data->you);
    $x = $currPosition->x;
    $y = $currPosition->y;
    switch($move) {
        case MOVE_LEFT:
            foreach($data->board->snakes as $snake) {
                //si alguna de las viboras esta cerca de la comida y si es mas grande o igual a mi devuelvo false
                if(($snake->length >= $data->you-> length) &&
                    (($snake->head->x == ($x - 2) && $snake->head->y == $y) ||
                    ($snake->head->x == ($x - 1) && $snake->head->y == ($y - 1)) ||
                    ($snake->head->x == ($x - 1) && $snake->head->y == ($y + 1)))) {
                    return true;
                }
            }
            break;
        case MOVE_RIGHT:
            foreach($data->board->snakes as $snake) {
                //si alguna de las viboras esta cerca de la comida y si es mas grande o igual a mi devuelvo false
                if(($snake->length >= $data->you-> length) &&
                    (($snake->head->x == ($x + 2) && $snake->head->y == $y) ||
                    ($snake->head->x == ($x + 1) && $snake->head->y == ($y - 1)) ||
                    ($snake->head->x == ($x + 1) && $snake->head->y == ($y + 1)))) {
                    return true;
                }
            }
            break;
        case MOVE_DOWN:
            foreach($data->board->snakes as $snake) {
                //si alguna de las viboras esta cerca de la comida y si es mas grande o igual a mi devuelvo false
                if(($snake->length >= $data->you-> length) &&
                    (($snake->head->x == $x && $snake->head->y == ($y - 2)) ||
                    ($snake->head->x == ($x - 1) && $snake->head->y == ($y - 1)) ||
                    ($snake->head->x == ($x + 1) && $snake->head->y == ($y - 1)))) {
                    return true;
                }
            }
            break;
        case MOVE_UP:
            foreach($data->board->snakes as $snake) {
                //si alguna de las viboras esta cerca de la comida y si es mas grande o igual a mi devuelvo false
                if(($snake->length >= $data->you-> length) &&
                    (($snake->head->x == $x && $snake->head->y == ($y + 2)) ||
                    ($snake->head->x == ($x - 1) && $snake->head->y == ($y + 1)) ||
                    ($snake->head->x == ($x + 1) && $snake->head->y == ($y + 1)))) {
                    return true;
                }
            }
            break;
        default:
            break;
    }
    return false;
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

  return nextMove($data,$nextDirection,13);
}

//[ 'left', 'up', 'down', 'right']
function nextMove($data,$nextMove,$tries) {
  $tries--;
  if($tries == 0)
    throw new \Exception('Can\'t move on any direction');

  $possibleMove = [ 'left', 'up', 'down', 'right'];
  //error_log('Checking: '.$possibleMove[$nextMove]);
  if(outOfBoundaries($data,$nextMove) || collide($data,$nextMove, $tries) || goingToCrashWithSnake($data,$nextMove)) {
    $nextMove = ++$nextMove % 4;
    $nextMove = nextMove($data,$nextMove,$tries);
  } else {
    return $nextMove;
  }

  return $nextMove;
}

