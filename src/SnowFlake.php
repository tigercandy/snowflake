<?php

namespace Mantis\Snowflake;

use swoole_lock;

class SnowFlake
{
    const SEQUENCE_BITS = 12;
    const SEQUENCE_MAX = -1 ^ (-1 << self::SEQUENCE_BITS);

    const WORKER_BITS = 10;
    const WORKER_MAX = -1 ^ (-1 << self::WORKER_BITS);

    const TIME_SHIFT = self::WORKER_BITS + self::SEQUENCE_BITS;
    const WORKER_SHIFT = self::SEQUENCE_BITS;

    protected $timestamp;
    protected $worker_id;
    protected $sequence;
    protected $lock;
    protected $epoch;

    /**
     * SnowFlake constructor.
     * @param $worker_id
     * @param int $epoch
     */
    public function __construct($worker_id, $epoch = 1546300800000) {
        if ($worker_id < 0 || $worker_id > self::WORKER_MAX) {
            trigger_error("ERROR: worker_id out of range.");
            exit(0);
        }

        $this->timestamp = 0;
        $this->worker_id = $worker_id;
        $this->sequence = 0;
        $this->lock = new swoole_lock(SWOOLE_MUTEX);
        $this->epoch = $epoch;
    }

    /**
     * Generate unique id.
     * @return int
     */
    public function create() {
        $this->lock->lock();
        $now = $this->now();
        if ($this->timestamp == $now) {
            $this->sequence++;
            if ($this->sequence > self::SEQUENCE_MAX) {
                while ($now <= $this->timestamp) {
                    $now = $this->now();
                }
            }
        } else {
            $this->sequence = 0;
        }

        $this->timestamp = $now;

        $id = (($now - $this->epoch) << self::TIME_SHIFT) | ($this->worker_id << self::WORKER_SHIFT) | $this->sequence;
        $this->lock->unlock();

        return $id;
    }

    /**
     * This time now.
     * @return string
     */
    public function now() {
        return sprintf("%.0f", microtime(true) * 1000);
    }
}