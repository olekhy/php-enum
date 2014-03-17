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
     * @param string $enumClass Classname of an enumeration the map is for
     * @throws InvalidArgumentException
     */
    public function __construct($enumClass)
    {
        if (!is_subclass_of($enumClass, __NAMESPACE__ . '\Enum')) {
            throw new InvalidArgumentException(sprintf(
                "This EnumMap can handle subclasses of '%s' only",
                __NAMESPACE__ . '\Enum'
            ));
        }

        $this->enumClass  = $enumClass;
        $this->ordinalMax = count($enumClass::getConstants());
    }

    /**
     * Get the classname of enumeration this map is for
     * @return string
     */
    public function getEnumClass()
    {
        return $this->enumClass;
    }

    /**
     * Attach a new enumeration or overwrite an existing one
     * @param Enum|scalar $enum
     * @return void
     * @throws InvalidArgumentException On an invalid given enum
     */
    public function attach($enum)
    {
        $enum = $this->initEnum($enum);
        $this->bitset |= 1 << $enum->getOrdinal();
    }

    /**
     * Detach all enumerations same as the given enum
     * @param Enum|scalar $enum
     * @return void
     * @throws InvalidArgumentException On an invalid given enum
     */
    public function detach($enum)
    {
        $enum = $this->initEnum($enum);
        $this->bitset &= ~(1 << $enum->getOrdinal());
    }

    /**
     * Test if the given enumeration exists
     * @param Enum|scalar $enum
     * @return boolean
     */
    public function contains($enum)
    {
        $enum = $this->initEnum($enum);
        return ($this->bitset & (1 << $enum->getOrdinal())) !== 0;
    }

    /* Iterator */

    /**
     * Get current Enum
     * @return Enum|null Returns current Enum or NULL on an invalid iterator position
     */
    public function current()
    {
        if ($this->bitset & (1 << $this->ordinal)) {
            $enumClass = $this->enumClass;
            return $enumClass::getByOrdinal($this->ordinal);
        } elseif (!$this->bitset || $this->ordinal === $this->ordinalMax) {
            return null;
        }

        do {
            if (++$this->ordinal === $this->ordinalMax) {
                return null;
            }
        } while(($this->bitset & (1 << $this->ordinal)) === 0);
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
        } elseif ($this->bitset && $this->ordinal !== $this->ordinalMax) {
            do {
                ++$this->ordinal;
            } while(($this->bitset & (1 << $this->ordinal)) === 0 && $this->ordinal !== $this->ordinalMax);
        }
        return $this->ordinal;
    }

    /**
     * Go to the next iterator position
     * @return void
     */
    public function next()
    {
        if ($this->bitset && $this->ordinal !== $this->ordinalMax) {
            do {
                ++$this->ordinal;
            } while(($this->bitset & (1 << $this->ordinal)) === 0 && $this->ordinal !== $this->ordinalMax);
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
        if ($this->bitset & (1 << $this->ordinal)) {
            return true;
        } elseif (!$this->bitset || $this->ordinal === $this->ordinalMax) {
            return false;
        }

        do {
            if (++$this->ordinal === $this->ordinalMax) {
                return false;
            }
        } while(($this->bitset & (1 << $this->ordinal)) === 0);
        return true;
    }

    /* Countable */

    /**
     * Count the number of elements
     * @return int
     */
    public function count()
    {
        $max = 1 << $this->ordinalMax;
        $cnt = 0;
        for ($bit = 1; $bit < $max; $bit = $bit << 1) {
            if ($this->bitset & $bit) {
                ++$cnt;
            }
        }
        return $cnt;
    }

    /**
     * Initialize an enumeration
     * @param Enum|scalar $enum
     * @return Enum
     * @throws InvalidArgumentException On an invalid given enum
     */
    private function initEnum($enum)
    {
        // auto instantiate
        if (is_scalar($enum)) {
            $enumClass = $this->enumClass;
            return $enumClass::get($enum);
        }

        // allow only enums of the same type
        // (don't allow instance of)
        $enumClass = get_class($enum);
        if ($enumClass && strcasecmp($enumClass, $this->enumClass) === 0) {
            return $enum;
        }

        throw new InvalidArgumentException(sprintf(
            "The given enum of type '%s' isn't same as the required type '%s'",
            get_class($enum) ?: gettype($enum),
            $this->enumClass
        ));
    }
}
