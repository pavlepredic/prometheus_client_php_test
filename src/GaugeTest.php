<?php

declare(strict_types=1);

use Prometheus\CollectorRegistry;
use Prometheus\Storage\InMemory;
use PHPUnit\Framework\TestCase;

class GaugeTest extends TestCase
{
    private CollectorRegistry $collectorRegistry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->collectorRegistry = new CollectorRegistry(new InMemory());
    }

    public function testGauge()
    {
        $gauge = $this->collectorRegistry->getOrRegisterGauge('test', 'foo', 'help');
        $gauge->set(1.0);
        $samples = $this->collectorRegistry->getMetricFamilySamples();
        self::assertCount(1, $samples);
        self::assertCount(1, $samples[0]->getSamples());
        self::assertSame(1.0, (float) $samples[0]->getSamples()[0]->getValue());

        //I thought maybe the second metric is ignored because it happens immediately, but sleep doesn't help either
        //sleep(10);
        $gauge->set(2.0);
        $samples = $this->collectorRegistry->getMetricFamilySamples();
        self::assertCount(1, $samples);
        self::assertCount(2, $samples[0]->getSamples());
        self::assertSame(1.0, (float) $samples[0]->getSamples()[0]->getValue());
        self::assertSame(2.0, (float) $samples[0]->getSamples()[1]->getValue());
    }
}
