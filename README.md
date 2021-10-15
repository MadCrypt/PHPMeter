# PHPMeter - PHP performance testing tool

A very simple package to check the performance of any PHP script.
 

## Installation
```console
$ composer require --dev vinay/phpmeter
```

## Usage
PHPMeter allows you to easily gauge the performance of PHP script's.



```php
<?php

include 'vendor/autoload';

// OR

use PHPMeter;

$app = new PHPMeter;

$app->step('start');

// some code...

$app->step('end');

$report = $app->getReport('start', 'end');

print_r($report);
```

Output:
```php
Array
(
    ['Clock time'] => 0:0:24
    ['Time taken in User Mode'] => 0:0:09
    ['Time taken in System Mode'] => 0:0:24
    ['Total time taken in Kernel'] => 0:0:24
    ['Memory limit in MB'] => -1
    ['Memory usage in MB'] => 0.002655029296875
    ['Peak memory usage in MB'] => 1.0958099365234
    ['Maximum resident shared size in KB'] => 0
    ['Integral shared memory size'] => 0
    ['Integral unshared data size'] => 0
    ['Integral unshared stack size'] => Not Available
    ['Number of page reclaims'] => 0
    ['Number of page faults'] => 0
    ['Number of block input operations'] => 0
    ['Number of block output operations'] => Not Available
    ['Number of messages sent'] => 0
    ['Number of messages received'] => 0
    ['Number of signals received'] => 0
    ['Number of voluntary context switches'] => 0
    ['Number of involuntary context switches'] => 1514
)
```

PHPMeter works by creating snapshots of time and system information in a given moment, known as *steps*, and then comparing two different steps. By using PHPMeter you can easily track your application performance in several different moments in an easy fashion, but it's still nothing short of an authentic benchmark tool such as Xdebug profiling capabilities.
