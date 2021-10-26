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
}
