<?php

namespace Dambrogia\CandlestickTest;

use PHPUnit\Framework\TestCase;
use Dambrogia\Candlestick\Collection;

final class CollectionTest extends TestCase
{
    use DataTrait;

    public function testCanCreate(): void
    {
        $this->assertInstanceOf(
            Collection::class,
            new Collection
        );
    }

    public function testGroupedData(): void
    {
        $items = $this->map([
            [0, 1, 2, 3],
            [0, 1, 2, 3],
            [4, 5, 6, 7],
            [4, 5, 6, 7],
        ]);

        $collection = new Collection;
        $collection->setItems($items);

        $expected = [
            [0.0, 0.0, 4.0, 4.0],
            [1.0, 1.0, 5.0, 5.0],
            [2.0, 2.0, 6.0, 6.0],
            [3.0, 3.0, 7.0, 7.0],
        ];

        $this->assertEquals($expected, $collection->grouped());
    }

    public function testRange()
    {
        $items = $this->map([
            [0, 0, 0, 0],
            [1, 1, 1, 1],
            [2, 2, 2, 2],
        ]);
        $collection = new Collection($items);

        $new = $collection->range(0, 1);
        $new2 = $collection->range(-1);

        $this->assertTrue($new->size() == 2);
        $this->assertTrue($new->get(1)->open() == 1);

        $this->assertTrue($new2->size() == 1);
        $this->assertTrue($new2->first()->open() == 2);
    }

    public function testFilter()
    {
        $items = $this->map([
            [0, 0, 0, 0],
            [1, 1, 1, 1],
            [2, 2, 2, 2],
        ]);


        $x = new Collection($items);
        $y = $x->filter(function ($candle) {
            return $candle->close() > 0;
        });

        // filter mutates the object, but also returns the same object (self).
        $this->assertTrue($x == $y);
        $this->assertTrue($x->size() == 2);
        $this->assertTrue($x->first()->open() == 1);
        $this->assertTrue($x->last()->open() == 2);
    }

    /**
     * Helper for creating collections to test with.
     * @param array $array
     * @return array
     */
    private function map(array $array): array
    {
        return array_map(function ($innerArr) {
            return [
                'open' => $innerArr[0],
                'high' => $innerArr[1],
                'low' => $innerArr[2],
                'close' => $innerArr[3],
                'volume' => 100,
                'date' => time(),
            ];
        }, $array);
    }
}
