<?php

namespace Mantis\Snowflake\Test;

use Chan;
use Mantis\Snowflake\SnowFlake;
use PHPUnit\Framework\TestCase;

class SnowflakeTest extends TestCase
{
    public function testSnowflake() {
        $snowflake = new SnowFlake(1);
        $chan = new chan(1000000);
        $n = 1000000;

        for ($i = 0; $i < $n; $i++) {
            go(function () use ($snowflake, $chan) {
                $id = $snowflake->create();
                $chan->push($id);
            });
        }

        go(function () use ($chan, $n) {
            $arr = [];
            for ($i = 0; $i < $n; $i++) {
                $id = $chan->pop();
                $this->assertFalse(in_array($id, $arr));
            }
            array_push($arr, $id);
        });

        $chan->close();
    }
}