<?php

namespace MabeEnum;

use Iterator;
use Countable;
use InvalidArgumentException;

/**
 * EnumSet implementation in base of SplObjectStorage
 *
 * @link http://github.com/marc-mabe/php-enum for the canonical source repository
 * @copyright Copyright (c) 2013 Marc Bennewitz
 * @license http://github.com/marc-mabe/php-enum/blob/master/LICENSE.txt New BSD License
 */
class EnumSet implements Iterator, Countable
{
    /**
     * Enumeration class
     * @var string
     */
    private $enumClass;

    /**
     * BitSet of all attached enumerations
     * @var int
     */
    private $bitset = 0;

    /**
     * Ordinal number of current iterator position
     * @var int
     */
    private $ordinal = 0;

    /**
     * Highest possible ordinal number
     * @var int
     */
    private $ordinalMax = 0;

    /**
     * Constructor
     *
     * @param string $enumClass Classname of an enumeration the set is for
     * @throws InvalidArgumentException
     */
    public function __construct($enumClass)
    {
        if (!is_subclass_of($enumClass, __NAMESPACE__ . '\Enum')) {
            throw new InvalidArgumentException(sprintf(
                "This EnumSet can handle subclasses of '%s' only",
                __NAMESPACE__ . '\Enum'
            ));
        }

        $this->enumClass  = $enumClass;
        $this->ordinalMax = count($enumClass::getConstants());
    }

    /**
     * Get the classname of enumeration this set is for
     * @return string
     */
    public function getEnumClass()
    {
        return $this->enumClass;
    }

    /**
     * Attach a new enumeration or overwrite an existing one
     * @param Enum|null|boolean|int|float|string $enum
     * @return void
     * @throws InvalidArgumentException On an invalid given enum
     */
    public function attach($enum)
    {
        $enumClass = $this->enumClass;
        $this->bitset |= 1 << $enumClass::get($enum)->getOrdinal();
    }

    /**
     * Detach all enumerations same as the given enum
     * @param Enum|null|boolean|int|float|string $enum
     * @return void
     * @throws InvalidArgumentException On an invalid given enum
     */
    public function detach($enum)
    {
        $enumClass = $this->enumClass;
        $this->bitset &= ~(1 << $enumClass::get($enum)->getOrdinal());
    }

    /**
     * Test if the given enumeration exists
     * @param Enum|null|boolean|int|float|string $enum
     * @return boolean
     */
    public function contains($enum)
    {
        $enumClass = $this->enumClass;
        return (bool)($this->bitset & (1 << $enumClass::get($enum)->getOrdinal()));
    }

    /* Iterator */

    /**
     * Get current Enum
     * @return Enum|null Returns current Enum or NULL on an invalid iterator position
     */
    public function current()
    {
        if (!$this->bitset || $this->ordinal === $this->ordinalMax) {
            return null;
        } elseif ($this->bitset & (1 << $this->ordinal)) {
            $enumClass = $this->enumClass;
            return $enumClass::getByOrdinal($this->ordinal);
        }

        do {
            ++$this->ordinal;
        } while(!($this->bitset & (1 << $this->ordinal)) && $this->ordinal !== $this->ordinalMax);
        if ($this->ordinal === $this->ordinalMax) {
            return null;
        }
        $enumClass = $this->enumClass;
        return $enumClass::getByOrdinal($this->ordinal);
    }

    /**
     * Get ordinal number of current iterator position
     * @return int
     */
    public function key()
    {
        if ($this->bitset & (1 << $this->ordinal)) {
            return $this->ordinal;
        }

        if ($this->bitset && $this->ordinal !== $this->ordinalMax) {
            do {
                ++$this->ordinal;
            } while(!($this->bitset & (1 << $this->ordinal)) && $this->ordinal !== $this->ordinalMax);
        }
        return $this->ordinal;
    }

    /**
     * Go to the next iterator position
     * @return void
     */
    public function next()
    {
        if ($this->ordinal !== $this->ordinalMax) {
            ++$this->ordinal;
        }
    }

    /**
     * Go to the first iterator position
     * @return void
     */
    public function rewind()
    {
        $this->ordinal = 0;
    }

    /**
     * Test if the iterator in a valid state
     * @return boolean
     */
    public function valid()
    {
        if (!$this->bitset || $this->ordinal === $this->ordinalMax) {
            return false;
        } elseif ($this->bitset & (1 << $this->ordinal)) {
            return true;
        }

        do {
            ++$this->ordinal;
        } while(!($this->bitset & (1 << $this->ordinal)) && $this->ordinal !== $this->ordinalMax);
        return $this->ordinal !== $this->ordinalMax;
    }

    /* Countable */

    /**
     * Count the number of elements
     * @return int
     */
    public function count()
    {
        $cnt = 0;
        $ord = 0;
        do {
            if ($this->bitset & (1 << $ord++)) {
                ++$cnt;
            }
        } while($ord !== $this->ordinalMax);

        return $cnt;
    }
}
