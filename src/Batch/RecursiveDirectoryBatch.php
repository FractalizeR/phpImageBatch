<?php
/*
 * ========================================================================
 * Copyright (c) 2011 Vladislav "FractalizeR" Rastrusny
 * Website: http://www.fractalizer.ru
 * Email: FractalizeR@yandex.ru
 * ------------------------------------------------------------------------
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================================
 */

namespace phpImageBatch\Batch;

use phpImageBatch\Image\Image;
use phpImageBatch\Backup\Backup;
use phpImageBatch\Marker\Marker;
use phpImageBatch\Processor\Processor;


class RecursiveDirectoryBatch {
    private $dirname;
    private $fileMasks;
    private $stamper;
    private $marker;
    private $backuper;

    public function __construct($dirname, array $fileMasks, Processor $stamper, Marker $marker, Backup $backuper) {
        $this->dirname = $dirname;
        $this->fileMasks = $fileMasks;
        $this->stamper = $stamper;
        $this->marker = $marker;
        $this->backuper = $backuper;
    }

    public function process() {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->dirname));

        foreach ($iterator as $file) {
            /* @var \SplFileInfo $file */
            if ($file->isDir()) {
                continue;
            }

            if (!$this->matchesMasks($file->getFilename())) {
                continue;
            }

            $filePathname = $file->getPathname();
            if ($this->marker->isMarkedProcessed($filePathname)) {
                continue;
            }

            $filePath = $file->getPath();
            if (!is_readable($filePath) or !is_writable($filePath)) {
                throw new BatchException(sprintf('Directory "%s" is not readable or writable!', $filePath));
            }

            if (!is_readable($filePathname) or !is_writable($filePathname)) {
                throw new BatchException(sprintf('File "%s" is not readable or writable!', $filePathname));
            }

            if (!$this->marker->canMarkProcessed($filePathname)) {
                throw new BatchException(sprintf('Cannot mark file "%s" as stamped!', $filePathname));
            }

            if (!$this->backuper->canBackup($filePathname)) {
                throw new BatchException(sprintf('Cannot backup file "%s"!', $filePathname));
            }

            $this->backuper->backup($filePathname);
            try {
                $image = Image::createFromFile($filePathname);
                $this->stamper->process($image);
                $image->saveToFile($filePathname);
                clearstatcache();
                $this->marker->markProcessed($filePathname);
            } catch (\Exception $e) {
                $this->backuper->restoreLastBackup($filePathname);
                $this->backuper->deleteLastBackup($filePathname);
                throw $e;
            }
        }
    }

    public function unprocess() {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->dirname));

        foreach ($iterator as $file) {
            /* @var \SplFileInfo $file */
            if ($file->isDir()) {
                continue;
            }

            if (!$this->matchesMasks($file->getFilename())) {
                continue;
            }

            $filePathname = $file->getPathname();
            if (!$this->marker->isMarkedProcessed($filePathname)) {
                continue;
            }

            $filePath = $file->getPath();
            if (!is_readable($filePath) or !is_writable($filePath)) {
                throw new BatchException(sprintf('Directory "%s" is not readable or writable!', $filePath));
            }

            if (!is_readable($filePathname) or !is_writable($filePathname)) {
                throw new BatchException(sprintf('File "%s" is not readable or writable!', $filePathname));
            }

            $this->backuper->restoreLastBackup($filePathname);
            $this->backuper->deleteLastBackup($filePathname);
            $this->marker->unmarkProcessed($filePathname);
            clearstatcache();
        }
    }


    function matchesMasks($fileBasename) {
        foreach ($this->fileMasks as $fileMask) {
            if (fnmatch($fileMask, $fileBasename)) {
                return true;
            }
        }
        return false;
    }
}