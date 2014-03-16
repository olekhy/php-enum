<?php

use MabeEnum\Enum;
use MabeEnum\EnumSet;
use MabeEnum\EnumList;

include __DIR__ . '/vendor/autoload.php';

class MyEnum extends Enum {
    const NIL = 0;
    const ONE = 1;
    const TWO = 2;
    const THREE = 3;
    const FOUR  = 4;
    const FIVE  = 5;
    const SIX   = 6;
    const SEVEN = 7;
    const EIGHT = 8;
    const NINE  = 9;
}

// defs + pre-run
$tStart = $mStart = $i = $obj = null;
$max    = 10000;
foreach (MyEnum::getConstants() as $k => $v) { MyEnum::get($v); }


// start bench
echo 'EnumList (unique, ordered):' . PHP_EOL;
echo '    instantiate:  ';
$tStart = microtime(true);
$mStart = memory_get_usage();
for ($i = 0; $i < $max; ++$i) {
    $obj = new EnumList('MyEnum', EnumList::UNIQUE | EnumList::ORDERED);
}
echo sprintf('%f', microtime(true) - $tStart) . ', mem: ' . (memory_get_usage() - $mStart) . PHP_EOL;

echo '    attach:       ';
$tStart = microtime(true);
$mStart = memory_get_usage();
for ($i = 0; $i < $max; ++$i) {
    $obj->attach(MyEnum::NIL);
    $obj->attach(MyEnum::ONE);
    $obj->attach(MyEnum::TWO);
    $obj->attach(MyEnum::THREE);
    $obj->attach(MyEnum::FOUR);
    $obj->attach(MyEnum::FIVE);
    $obj->attach(MyEnum::SIX);
    $obj->attach(MyEnum::SEVEN);
    $obj->attach(MyEnum::EIGHT);
    $obj->attach(MyEnum::NINE);
}
echo sprintf('%f', microtime(true) - $tStart) . ', mem: ' . (memory_get_usage() - $mStart) . PHP_EOL;

echo '    contains:     ';
$tStart = microtime(true);
$mStart = memory_get_usage();
for ($i = 0; $i < $max; ++$i) {
    foreach (MyEnum::getConstants() as $k => $v) {
        $obj->contains($v);
    }
}
echo sprintf('%f', microtime(true) - $tStart) . ', mem: ' . (memory_get_usage() - $mStart) . PHP_EOL;

echo '    iterate v:    ';
$tStart = microtime(true);
$mStart = memory_get_usage();
for ($i = 0; $i < $max; ++$i) {
    foreach ($obj as $v) {}
}
echo sprintf('%f', microtime(true) - $tStart) . ', mem: ' . (memory_get_usage() - $mStart) . PHP_EOL;

echo '    iterate k=>v: ';
$tStart = microtime(true);
$mStart = memory_get_usage();
for ($i = 0; $i < $max; ++$i) {
    foreach ($obj as $k => $v) {}
}
echo sprintf('%f', microtime(true) - $tStart) . ', mem: ' . (memory_get_usage() - $mStart) . PHP_EOL;

echo '    count:        ';
$tStart = microtime(true);
$mStart = memory_get_usage();
for ($i = 0; $i < $max; ++$i) {
    $obj->count();
}
echo sprintf('%f', microtime(true) - $tStart) . ', mem: ' . (memory_get_usage() - $mStart) . PHP_EOL;


echo PHP_EOL . 'EnumSet:' . PHP_EOL;
echo '    instantiate:  ';
$tStart = microtime(true);
$mStart = memory_get_usage();
for ($i = 0; $i < $max; ++$i) {
    $obj = new EnumSet('MyEnum');
}
echo sprintf('%f', microtime(true) - $tStart) . ', mem: ' . (memory_get_usage() - $mStart) . PHP_EOL;

echo '    attach:       ';
$start = microtime(true);
$mStart = memory_get_usage();
for ($i = 0; $i < $max; ++$i) {
    $obj->attach(MyEnum::NIL);
    $obj->attach(MyEnum::ONE);
    $obj->attach(MyEnum::TWO);
    $obj->attach(MyEnum::THREE);
    $obj->attach(MyEnum::FOUR);
    $obj->attach(MyEnum::FIVE);
    $obj->attach(MyEnum::SIX);
    $obj->attach(MyEnum::SEVEN);
    $obj->attach(MyEnum::EIGHT);
    $obj->attach(MyEnum::NINE);
}
echo sprintf('%f', microtime(true) - $tStart) . ', mem: ' . (memory_get_usage() - $mStart) . PHP_EOL;

echo '    contains:     ';
$tStart = microtime(true);
$mStart = memory_get_usage();
for ($i = 0; $i < $max; ++$i) {
    foreach (MyEnum::getConstants() as $k => $v) {
        $obj->contains($v);
    }
}
echo sprintf('%f', microtime(true) - $tStart) . ', mem: ' . (memory_get_usage() - $mStart) . PHP_EOL;

echo '    iterate v:    ';
$tStart = microtime(true);
$mStart = memory_get_usage();
for ($i = 0; $i < $max; ++$i) {
    foreach ($obj as $v) { }
}
echo sprintf('%f', microtime(true) - $tStart) . ', mem: ' . (memory_get_usage() - $mStart) . PHP_EOL;

echo '    iterate k=>v: ';
$tStart = microtime(true);
$mStart = memory_get_usage();
for ($i = 0; $i < $max; ++$i) {
    foreach ($obj as $k => $v) { }
}
echo sprintf('%f', microtime(true) - $tStart) . ', mem: ' . (memory_get_usage() - $mStart) . PHP_EOL;

echo '    count:        ';
$tStart = microtime(true);
$mStart = memory_get_usage();
for ($i = 0; $i < $max; ++$i) {
    $obj->count();
}
echo sprintf('%f', microtime(true) - $tStart) . ', mem: ' . (memory_get_usage() - $mStart) . PHP_EOL;

