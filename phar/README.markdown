Phar files
=======================================

What is this?
---------------------------------------

This directory contains phpImageBatch phar archive files. Phars are self-sufficient
and contain all the library files in it. You can use them alone without anything else.

The only difference between three of them is compression.

*   .phar is uncompressed, you can use it with any PHP configuration
that supports .phar files.
*   .phar.bz2 is bzip2 compressed and to work it requires bzip2 extension to be installed.
*   .phar.gz is gzip compressed and to work it requires gzip extension to be present.


Where to get?
---------------------------------------

You can download most recent library phar files from project download area
http://github.com/FractalizeR/phpImageBatch/archives/master

Or you can use Phing to build them yourself. To do this please read README file in the phing directory.

Usage
---------------------------------------

```php
<?php
require_once("phpImageBatch.phar');
$fileMasks = array('*.jpg', '*.png', '*.gif');
$logo = Image::createFromFile((__DIR__ . '/logo.jpg'));
$stamper = new SimpleStamp($logo);
$marker = new FileMarker();
$backuper = new SimpleFileBackup();
$processor = new RecursiveDirectoryProcessor(__DIR__ . '/images', $fileMasks, $stamper, $marker, $backuper);

$processor->process(__DIR__ . '/images');
```