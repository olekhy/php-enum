<?php

namespace MabeEnumTest;

use MabeEnum\Enum;
use MabeEnum\EnumList;
use MabeEnumTest\TestAsset\EnumBasic;
use MabeEnumTest\TestAsset\EnumInheritance;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Unit tests for the class MabeEnum\EnumList
 *
 * @link http://github.com/marc-mabe/php-enum for the canonical source repository
 * @copyright Copyright (c) 2013 Marc Bennewitz
 * @license http://github.com/marc-mabe/php-enum/blob/master/LICENSE.txt New BSD License
 */
class EnumListTest extends TestCase
{
    public function testBasic()
    {
        $enumList = new EnumList('MabeEnumTest\TestAsset\EnumBasic');
        $this->assertSame('MabeEnumTest\TestAsset\EnumBasic', $enumList->getEnumClass());
        $this->assertSame(EnumList::UNIQUE, $enumList->getFlags());

        $enum1  = EnumBasic::ONE();
        $enum2  = EnumBasic::TWO();

        $this->assertFalse($enumList->contains($enum1));
        $this->assertNull($enumList->attach($enum1));
        $this->assertTrue($enumList->contains($enum1));

        $this->assertFalse($enumList->contains($enum2));
        $this->assertNull($enumList->attach($enum2));
        $this->assertTrue($enumList->contains($enum2));

        $this->assertNull($enumList->detach($enum1));
        $this->assertFalse($enumList->contains($enum1));

        $this->assertNull($enumList->detach($enum2));
        $this->assertFalse($enumList->contains($enum2));
    }

    public function testBasicWithConstantValuesAsEnums()
    {
        $enumList = new EnumList('MabeEnumTest\TestAsset\EnumBasic');

        $enum1 = EnumBasic::ONE;
        $enum2 = EnumBasic::TWO;

        $this->assertFalse($enumList->contains($enum1));
        $this->assertNull($enumList->attach($enum1));
        $this->assertTrue($enumList->contains($enum1));

        $this->assertFalse($enumList->contains($enum2));
        $this->assertNull($enumList->attach($enum2));
        $this->assertTrue($enumList->contains($enum2));

        $this->assertNull($enumList->detach($enum1));
        $this->assertFalse($enumList->contains($enum1));

        $this->assertNull($enumList->detach($enum2));
        $this->assertFalse($enumList->contains($enum2));
    }

    public function testUnique()
    {
        $enumList = new EnumList('MabeEnumTest\TestAsset\EnumBasic', EnumList::UNIQUE);
        $this->assertSame(EnumList::UNIQUE, $enumList->getFlags());

        $enumList->attach(EnumBasic::ONE());
        $enumList->attach(EnumBasic::ONE);

        $enumList->attach(EnumBasic::TWO());
        $enumList->attach(EnumBasic::TWO);

        $this->assertSame(2, $enumList->count());
    }

    public function testNotUnique()
    {
        $enumList = new EnumList('MabeEnumTest\TestAsset\EnumBasic', 0);
        $this->assertSame(0, $enumList->getFlags());

        $enumList->attach(EnumBasic::ONE());
        $enumList->attach(EnumBasic::ONE);

        $enumList->attach(EnumBasic::TWO());
        $enumList->attach(EnumBasic::TWO);

        $this->assertSame(4, $enumList->count());

        // detch remove all
        $enumList->detach(EnumBasic::ONE);
        $this->assertSame(2, $enumList->count());
    }

    public function testIterateUnordered()
    {
        $enumList = new EnumList('MabeEnumTest\TestAsset\EnumBasic', EnumList::UNIQUE);

        $enum1 = EnumBasic::ONE();
        $enum2 = EnumBasic::TWO();

        // an empty enum set needs to be invalid, starting by 0
        $this->assertSame(0, $enumList->count());
        $this->assertFalse($enumList->valid());
        $this->assertNull($enumList->current());

        // attach
        $enumList->attach($enum1);
        $enumList->attach($enum2);

        // a not empty enum map should be valid, starting by 0 (if not iterated)
        $this->assertSame(2, $enumList->count());
        $this->assertTrue($enumList->valid());
        $this->assertSame(0, $enumList->key());
        $this->assertSame($enum1, $enumList->current());

        // go to the next element (last)
        $this->assertNull($enumList->next());
        $this->assertTrue($enumList->valid());
        $this->assertSame(1, $enumList->key());
        $this->assertSame($enum2, $enumList->current());

        // go to the next element (out of range)
        $this->assertNull($enumList->next());
        $this->assertFalse($enumList->valid());
        $this->assertSame(2, $enumList->key());
        $this->assertNull($enumList->current());

        // rewind will set the iterator position back to 0
        $enumList->rewind();
        $this->assertTrue($enumList->valid());
        $this->assertSame(0, $enumList->key());
        $this->assertSame($enum1, $enumList->current());
    }

    public function testIterateOrdered()
    {
        $enumList = new EnumList('MabeEnumTest\TestAsset\EnumBasic', EnumList::UNIQUE | EnumList::ORDERED);

        $enum1 = EnumBasic::ONE();
        $enum2 = EnumBasic::TWO();

        // an empty enum set needs to be invalid, starting by 0
        $this->assertSame(0, $enumList->count());
        $this->assertFalse($enumList->valid());
        $this->assertNull($enumList->current());

        // attach
        $enumList->attach($enum2);
        $enumList->attach($enum1);

        // a not empty enum map should be valid, starting by 0 (if not iterated)
        $this->assertSame(2, $enumList->count());
        $this->assertTrue($enumList->valid());
        $this->assertSame(0, $enumList->key());
        $this->assertSame($enum1, $enumList->current());

        // go to the next element (last)
        $this->assertNull($enumList->next());
        $this->assertTrue($enumList->valid());
        $this->assertSame(1, $enumList->key());
        $this->assertSame($enum2, $enumList->current());

        // go to the next element (out of range)
        $this->assertNull($enumList->next());
        $this->assertFalse($enumList->valid());
        $this->assertSame(2, $enumList->key());
        $this->assertNull($enumList->current());

        // rewind will set the iterator position back to 0
        $enumList->rewind();
        $this->assertTrue($enumList->valid());
        $this->assertSame(0, $enumList->key());
        $this->assertSame($enum1, $enumList->current());
    }

    public function testIterateOrderedNotUnique()
    {
        $enumList = new EnumList('MabeEnumTest\TestAsset\EnumBasic', EnumList::ORDERED);

        $enum1 = EnumBasic::ONE();
        $enum2 = EnumBasic::TWO();

        // an empty enum set needs to be invalid, starting by 0
        $this->assertSame(0, $enumList->count());
        $this->assertFalse($enumList->valid());
        $this->assertNull($enumList->current());

        // attach
        $enumList->attach($enum2);
        $enumList->attach($enum1);
        $enumList->attach($enum2);
        $enumList->attach($enum1);

        // index 0
        $this->assertSame(4, $enumList->count());
        $this->assertTrue($enumList->valid());
        $this->assertSame(0, $enumList->key());
        $this->assertSame($enum1, $enumList->current());

        // index 1
        $this->assertNull($enumList->next());
        $this->assertTrue($enumList->valid());
        $this->assertSame(1, $enumList->key());
        $this->assertSame($enum1, $enumList->current());

        // index 2
        $this->assertNull($enumList->next());
        $this->assertTrue($enumList->valid());
        $this->assertSame(2, $enumList->key());
        $this->assertSame($enum2, $enumList->current());

        // index 3 (last)
        $this->assertNull($enumList->next());
        $this->assertTrue($enumList->valid());
        $this->assertSame(3, $enumList->key());
        $this->assertSame($enum2, $enumList->current());

        // go to the next element (out of range)
        $this->assertNull($enumList->next());
        $this->assertFalse($enumList->valid());
        $this->assertSame(4, $enumList->key());
        $this->assertNull($enumList->current());

        // rewind will set the iterator position back to 0
        $enumList->rewind();
        $this->assertTrue($enumList->valid());
        $this->assertSame(0, $enumList->key());
        $this->assertSame($enum1, $enumList->current());
    }

    public function testIterateAndDetach()
    {
        $enumList = new EnumList('MabeEnumTest\TestAsset\EnumInheritance');

        $enum1 = EnumInheritance::ONE();
        $enum2 = EnumInheritance::TWO();
        $enum3 = EnumInheritance::INHERITANCE();

        // attach
        $enumList->attach($enum1);
        $enumList->attach($enum2);
        $enumList->attach($enum3);

        // index 1
        $enumList->next();
        $this->assertSame($enum2, $enumList->current());

        // detach enum of current index
        $enumList->detach($enumList->current());
        $this->assertSame($enum3, $enumList->current());

        // detach enum of current index if the last index
        $enumList->detach($enumList->current());
        $this->assertFalse($enumList->valid());
        $this->assertNull($enumList->current());
    }

    public function testConstructThrowsInvalidArgumentExceptionIfEnumClassDoesNotExtendBaseEnum()
    {
        $this->setExpectedException('InvalidArgumentException');
        new EnumList('stdClass');
    }

    public function testInitEnumThrowsInvalidArgumentExceptionOnInvalidEnum()
    {
        $enumList = new EnumList('MabeEnumTest\TestAsset\EnumBasic');
        $this->setExpectedException('InvalidArgumentException');
        $this->assertFalse($enumList->contains(EnumInheritance::INHERITANCE()));
    }
}
