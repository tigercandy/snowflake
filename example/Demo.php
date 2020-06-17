<?php

use Mantis\Snowflake\Snowflake;

class Demo {
	
	public function create() {
		$snowflake = new Snowflake(1);

		return $snowflake->create();
	}
}

$demo = new Demo();

print_r($demo->create());