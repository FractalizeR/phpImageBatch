phpImageBatch
=======================================

phpImageBatch is a small framework to build batch file processors. Mostly image processors.

Currently it can:

*   Backup files before processing
*   Mark files as processed so that you will not process them twice
*   Batch recursively process directory

Currently library contains one processor named 'LogoStamp' which I use to place the logo of my company to various
pictures in variouses CMS systems.

Library is fully modular and each module can be extended independently.

There is also a possibility to rollback an operation

Modules
---------------------------------------

The library consists of the following modules:

*   Backup - a module, which manages backup operations. Currently there is a SimpleFileBackup class which does backup
to a file with the same name plus some additional extension and NullBackup class which does no backups at all
*   Marker - a module responsible for marking files as processed. Currently there is a SimpleFileMarker which marks
files as processed by creating a touch()'ed file with the same name plus some additional extension. Also there is
a NullMarker class, which doesn't mark anything
*   Image - a module which incapsulates GD image
*   Processor - a module which actually performs some operation over a single file. Currently there is a LogoStamp
processor which can place custom picture over given one preserving alpha-state. I use it to place logos on
a number of pictures.
*   Batch - a module which executes some Processor over some number of files. Currently there is
RecursiveDirectoryBatch which goes over all files in a directory recursively

Usage example
---------------------------------------

```php
<?php

namespace phpImageBatch;

use \phpImageBatch\Batch\RecursiveDirectoryBatch;
use \phpImageBatch\Processor\LogoStamp;
use \phpImageBatch\Image\Image;
use \phpImageBatch\Marker\SimpleFileMarker;
use \phpImageBatch\Backup\SimpleFileBackup;

//require_once __DIR__ . '/../src/Autoloader.php';
//Autoloader::register();

require_once __DIR__ . '/../phar/phpImageBatch.phar';

$fileMasks = array('*.jpg', '*.png', '*.gif');
$logo = Image::createFromFile(__DIR__ . '/baza-logo.png');
$stamper = new LogoStamp($logo);
$marker = new SimpleFileMarker('.stamp');
$backuper = new SimpleFileBackup('.stampbackup');

$processor = new RecursiveDirectoryBatch(__DIR__ . '/images', $fileMasks, $stamper, $marker, $backuper);
$processor->process(); //Performs operation
$processor->unprocess(); //Rolling back an operation
```