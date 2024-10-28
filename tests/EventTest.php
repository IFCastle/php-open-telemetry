<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function testConstruct(): void
    {
        $event                      = new Event('test', ['key' => 'value']);

        $this->assertSame('test', $event->getName());
        $this->assertSame(['key' => 'value'], $event->getAttributes());
        $this->assertLessThanOrEqual(SystemClock::now(), $event->getTimeUnixNano());
    }

    public function testConstructWithTimestamp(): void
    {
        $timestamp                  = SystemClock::now();
        $event                      = new Event('test', ['key' => 'value'], $timestamp);

        $this->assertSame('test', $event->getName());
        $this->assertSame(['key' => 'value'], $event->getAttributes());
        $this->assertSame($timestamp, $event->getTimeUnixNano());
    }
}
