<?php

declare(strict_types=1);

use Boctulus\Simplerest\Core\Libs\Date;
use Boctulus\Simplerest\Core\Libs\SortedIterator;
use PHPUnit\Framework\TestCase;

/**
 * Contract for the two core methods that blocked a clean PHPStan run on the
 * 8.3 baseline. Both are runtime-visible, so they get behavioural tests rather
 * than a baseline entry.
 */
final class Php83CompatibilityTest extends TestCase
{
    public function testSortedIteratorDeclaresACovariantCompareReturnType(): void
    {
        $method = new ReflectionMethod(SortedIterator::class, 'compare');

        self::assertSame('int', (string) $method->getReturnType());
    }

    public function testSortedIteratorSortsByRealPath(): void
    {
        $dir = sys_get_temp_dir() . '/simplerest-sorted-' . bin2hex(random_bytes(4));
        mkdir($dir, 0775, true);

        $names = ['c.txt', 'a.txt', 'b.txt'];
        foreach ($names as $name) {
            file_put_contents($dir . '/' . $name, $name);
        }

        $files = [];
        foreach ($names as $name) {
            $files[] = new SplFileInfo($dir . '/' . $name);
        }

        $sorted = [];
        foreach (new SortedIterator(new ArrayIterator($files)) as $file) {
            $sorted[] = $file->getFilename();
        }

        foreach ($names as $name) {
            unlink($dir . '/' . $name);
        }
        rmdir($dir);

        self::assertCount(3, $sorted);
        self::assertSame(['a.txt', 'b.txt', 'c.txt'], $sorted);
    }

    public function testNextNthWeekDayReturnsAFormattedDateForEveryWeekday(): void
    {
        for ($weekday = 0; $weekday <= 6; $weekday++) {
            $result = Date::nextNthWeekDay($weekday, '', 'Y-m-d');

            self::assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $result, "weekday=$weekday");
        }
    }

    public function testNextNthWeekDayMatchesTheRequestedWeekday(): void
    {
        for ($weekday = 0; $weekday <= 6; $weekday++) {
            $result = Date::nextNthWeekDay($weekday, '', 'w');

            self::assertSame((string) $weekday, $result);
        }
    }
}
