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

namespace phpImageBatch\Backup;

/**
 * A class which does backups to the files at the same folder but with .batchbackup extension appended
 * @throws BackupException
 */

class SimpleFileBackup implements Backup {

    private $backupFilenameSuffix;

    public function __construct($backupFilenameSuffix = '.batchbackup') {
        $this->backupFilenameSuffix = $backupFilenameSuffix;
    }

    public function backup($uri) {
        @unlink($this->getBackupFilename($uri));
        if (!copy($uri, $this->getBackupFilename($uri))) {
            throw new BackupException(sprintf('Cannot backup file "%s" to "%s"', $uri, $this->getBackupFilename($uri)));
        }
    }

    public function canBackup($uri) {
        return is_writable(dirname($this->getBackupFilename($uri)));
    }

    public function deleteLastBackup($uri) {
        @unlink($this->getBackupFilename($uri));
    }

    public function restoreLastBackup($uri) {
        @unlink($uri);
        if (!rename($this->getBackupFilename($uri), $uri)) {
            throw new BackupException(sprintf('Cannot restore backup file "%s" to "%s"!', $uri,
                    $this->getBackupFilename($uri)));
        }
    }

    /**
     * Generates backup file name
     *
     * @param string $filename
     * @return string Generated backup filename
     */
    private function getBackupFilename($filename) {
        return $filename . $this->backupFilenameSuffix;
    }
}
