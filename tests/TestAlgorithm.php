<?php

require_once "../algorithm.php";


use PHPUnit\Framework\TestCase;

class TestAlgorithm extends TestCase
{
    public function testTrueSomethingInPath() {
        $json = '{"board":{"height":11,"width":11,"snakes":[],"food":[{"x":0,"y":0}],"hazards":[]},"you":{"health":99,"body":[{"x":0,"y":4},{"x":1,"y":4},{"x":1,"y":3},{"x":1,"y":2},{"x":0,"y":2},{"x":0,"y":1}],"head":{"x":0,"y":4},"length":6,"shout":"","squad":""}}';
        $data = json_decode($json);

        $this->assertTrue(somethingInPath($data, $data->you->head, MOVE_DOWN));
        $this->assertFalse(somethingInPath($data, $data->you->head, MOVE_UP));
        $this->assertTrue(somethingInPath($data, $data->you->head, MOVE_RIGHT));
        $this->assertFalse(somethingInPath($data, $data->you->head, MOVE_LEFT));
    }

    public function testImpasseDown() {
        $json = '{"board":{"height":11,"width":11,"snakes":[],"food":[{"x":0,"y":0}],"hazards":[]},"you":{"health":99,"body":[{"x":0,"y":4},{"x":1,"y":4},{"x":1,"y":3},{"x":1,"y":2},{"x":0,"y":2},{"x":0,"y":1}],"head":{"x":0,"y":4},"length":6,"shout":"","squad":""}}';
        $data = json_decode($json);

        $currPosition = currentPosition($data->you);
        $nextDirection = translate(MOVE_DOWN);
        $nextPosition = arrayMergeNumericValues($currPosition,$nextDirection);
        $this->assertFalse(isMyBody($data->you->body, $nextPosition));
        $this->assertFalse(thereIsAnotherSnake($data->board->snakes, $nextPosition));
        $this->assertTrue(impasse($data, $nextPosition, MOVE_DOWN, $currPosition));
    }

    public function testImpasseUp() {
        $json = '{"board":{"height":11,"width":11,"snakes":[],"food":[{"x":0,"y":0}],"hazards":[]},"you":{"health":99,"body":[{"x":0,"y":4},{"x":1,"y":4},{"x":1,"y":3},{"x":1,"y":2},{"x":0,"y":2},{"x":0,"y":1}],"head":{"x":0,"y":4},"length":6,"shout":"","squad":""}}';
        $data = json_decode($json);

        $currPosition = currentPosition($data->you);
        $nextDirection = translate(MOVE_UP);
        $nextPosition = arrayMergeNumericValues($currPosition,$nextDirection);
        $this->assertFalse(isMyBody($data->you->body, $nextPosition));
        $this->assertFalse(thereIsAnotherSnake($data->board->snakes, $nextPosition));
        $this->assertFalse(impasse($data, $nextPosition, MOVE_UP, $currPosition));
    }

    public function testGoingToCrash() {
        $json = '{"game":{"id":"cb07e559-19fa-498b-a345-f075e63c1a42","ruleset":{"name":"standard","version":"v1.0.22","settings":{"foodSpawnChance":15,"minimumFood":1,"hazardDamagePerTurn":0,"royale":{"shrinkEveryNTurns":0},"squad":{"allowBodyCollisions":false,"sharedElimination":false,"sharedHealth":false,"sharedLength":false}}},"timeout":500,"source":"custom"},"turn":81,"board":{"height":11,"width":11,"snakes":[{"id":"gs_xVhYRQWkybHy9WFqjjSdTf7B","name":"LaChangaNeta","latency":"250","health":93,"body":[{"x":3,"y":2},{"x":2,"y":2},{"x":2,"y":1},{"x":2,"y":0},{"x":1,"y":0},{"x":1,"y":1},{"x":1,"y":2},{"x":1,"y":3},{"x":1,"y":4},{"x":2,"y":4},{"x":2,"y":5},{"x":3,"y":5},{"x":3,"y":6},{"x":4,"y":6},{"x":5,"y":6}],"head":{"x":3,"y":2},"length":15,"shout":"","squad":""},{"id":"gs_S6MVFbRjkFKCphVhkgg9KY47","name":"GGGelo","latency":"190","health":83,"body":[{"x":3,"y":4},{"x":4,"y":4},{"x":4,"y":5},{"x":5,"y":5},{"x":5,"y":4},{"x":5,"y":3},{"x":5,"y":2},{"x":5,"y":1},{"x":6,"y":1},{"x":7,"y":1},{"x":8,"y":1}],"head":{"x":3,"y":4},"length":11,"shout":"","squad":""}],"food":[{"x":1,"y":8},{"x":2,"y":8}],"hazards":[]},"you":{"id":"gs_xVhYRQWkybHy9WFqjjSdTf7B","name":"LaChangaNeta","latency":"250","health":93,"body":[{"x":3,"y":2},{"x":2,"y":2},{"x":2,"y":1},{"x":2,"y":0},{"x":1,"y":0},{"x":1,"y":1},{"x":1,"y":2},{"x":1,"y":3},{"x":1,"y":4},{"x":2,"y":4},{"x":2,"y":5},{"x":3,"y":5},{"x":3,"y":6},{"x":4,"y":6},{"x":5,"y":6}],"head":{"x":3,"y":2},"length":15,"shout":"","squad":""}}';
        $data = json_decode($json);
        $this->assertFalse(goingToCrashWithSnake($data, MOVE_UP));

    }

    public function testCornerCase() {
        $json = '{"board":{"height":11,"width":11,"snakes":[],"food":[],"hazards":[]},"you":{"health":99,"body":[{"x":10,"y":1},{"x":9,"y":1},{"x":9,"y":0},{"x":9,"y":0},{"x":7,"y":0},{"x":6,"y":0}],"head":{"x":10,"y":1},"length":6,"shout":"","squad":""}}';
        $data = json_decode($json);
        $this->assertFalse(goingToCrashWithSnake($data, MOVE_UP));

        $data = json_decode($json);

        $currPosition = currentPosition($data->you);
        $nextDirection = translate(MOVE_DOWN);
        $nextPosition = arrayMergeNumericValues($currPosition,$nextDirection);
        $this->assertFalse(isMyBody($data->you->body, $nextPosition));
        $this->assertFalse(thereIsAnotherSnake($data->board->snakes, $nextPosition));
        $this->assertTrue(impasse($data, $nextPosition, MOVE_DOWN, $currPosition));
        $this->assertTrue(conflict($data, $nextPosition, MOVE_DOWN, $currPosition));
    }
    public function testFoodCornerCase() {
        $json = '{"board":{"height":11,"width":11,"snakes":[],"food":[{"x":10,"y":0}],"hazards":[]},"you":{"health":99,"body":[{"x":10,"y":1},{"x":10,"y":2}],"head":{"x":10,"y":1},"length":2,"shout":"","squad":""}}';
        $data = json_decode($json);
        $this->assertFalse(goingToCrashWithSnake($data, MOVE_UP));

        $data = json_decode($json);

        $currPosition = currentPosition($data->you);
        $nextDirection = translate(MOVE_DOWN);
        $nextPosition = arrayMergeNumericValues($currPosition,$nextDirection);
        $this->assertFalse(isMyBody($data->you->body, $nextPosition));
        $this->assertFalse(thereIsAnotherSnake($data->board->snakes, $nextPosition));
        $this->assertFalse(impasse($data, $nextPosition, MOVE_DOWN, $currPosition));
        $this->assertFalse(conflict($data, $nextPosition, MOVE_DOWN, $currPosition));
    }


    public function test() {
        $json = '{"game":{"id":"daf25a6c-1803-489c-afc8-8991b860f53c","ruleset":{"name":"standard","version":"v1.0.22","settings":{"foodSpawnChance":15,"minimumFood":1,"hazardDamagePerTurn":0,"royale":{"shrinkEveryNTurns":0},"squad":{"allowBodyCollisions":false,"sharedElimination":false,"sharedHealth":false,"sharedLength":false}}},"timeout":500,"source":"custom"},"turn":79,"board":{"height":11,"width":11,"snakes":[{"id":"gs_QXdCkJJBpp7xTdgY8vJVdymG","name":"GGGelo","latency":"219","health":89,"body":[{"x":4,"y":3},{"x":5,"y":3},{"x":5,"y":2},{"x":5,"y":1},{"x":5,"y":0},{"x":6,"y":0},{"x":7,"y":0},{"x":8,"y":0},{"x":9,"y":0},{"x":10,"y":0},{"x":10,"y":1},{"x":10,"y":2}],"head":{"x":4,"y":3},"length":12,"shout":"","squad":""},{"id":"gs_vKBBWp3WtPr6txGyxqVxTydJ","name":"LaChangaNeta","latency":"245","health":99,"body":[{"x":9,"y":6},{"x":9,"y":5},{"x":8,"y":5},{"x":7,"y":5},{"x":6,"y":5},{"x":5,"y":5},{"x":5,"y":6},{"x":6,"y":6},{"x":6,"y":7},{"x":7,"y":7},{"x":8,"y":7},{"x":8,"y":8}],"head":{"x":9,"y":6},"length":12,"shout":"","squad":""}],"food":[{"x":0,"y":0}],"hazards":[]},"you":{"id":"gs_vKBBWp3WtPr6txGyxqVxTydJ","name":"LaChangaNeta","latency":"245","health":99,"body":[{"x":9,"y":6},{"x":9,"y":5},{"x":8,"y":5},{"x":7,"y":5},{"x":6,"y":5},{"x":5,"y":5},{"x":5,"y":6},{"x":6,"y":6},{"x":6,"y":7},{"x":7,"y":7},{"x":8,"y":7},{"x":8,"y":8}],"head":{"x":9,"y":6},"length":12,"shout":"","squad":""}}';
        $data = json_decode($json);

        $currPosition = currentPosition($data->you);
        $nextDirection = translate(MOVE_UP);
        $nextPosition = arrayMergeNumericValues($currPosition,$nextDirection);
        $this->assertFalse(isMyBody($data->you->body, $nextPosition));
        $this->assertFalse(thereIsAnotherSnake($data->board->snakes, $nextPosition));
        $this->assertFalse(impasse($data, $nextPosition, MOVE_UP, $currPosition));
        $this->assertFalse(conflict($data, $nextPosition, MOVE_UP, $currPosition));

        $nextDirection = translate(MOVE_DOWN);
        $nextPosition = arrayMergeNumericValues($currPosition,$nextDirection);
        $this->assertTrue(isMyBody($data->you->body, $nextPosition));
        $this->assertTrue(thereIsAnotherSnake($data->board->snakes, $nextPosition));
        $this->assertFalse(impasse($data, $nextPosition, MOVE_DOWN, $currPosition));
        $this->assertTrue(conflict($data, $nextPosition, MOVE_DOWN, $currPosition));

        $nextDirection = translate(MOVE_RIGHT);
        $nextPosition = arrayMergeNumericValues($currPosition,$nextDirection);
        $this->assertFalse(isMyBody($data->you->body, $nextPosition));
        $this->assertFalse(thereIsAnotherSnake($data->board->snakes, $nextPosition));
        $this->assertFalse(impasse($data, $nextPosition, MOVE_RIGHT, $currPosition));
        $this->assertFalse(conflict($data, $nextPosition, MOVE_RIGHT, $currPosition));

        $nextDirection = translate(MOVE_LEFT);
        $nextPosition = arrayMergeNumericValues($currPosition,$nextDirection);
        $this->assertFalse(isMyBody($data->you->body, $nextPosition));
        $this->assertFalse(thereIsAnotherSnake($data->board->snakes, $nextPosition));
        $this->assertFalse(impasse($data, $nextPosition, MOVE_LEFT, $currPosition));
        $this->assertFalse(conflict($data, $nextPosition, MOVE_LEFT, $currPosition));
    }

}
