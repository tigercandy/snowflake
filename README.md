### Snowflake for PHP.
***
[![Version](https://img.shields.io/badge/version-1.0.0-green.svg)](https://github.com/chunlintang/snowflake)

#### Required

- php >= 7.0.0
- swoole >= 4.0.0

#### Use

```php
use Mantis\Snowflake\Snowflake;

$snowflake = new SnowFlake(1);
$id = $snowflake->create();
```

#### Test

```php
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
```