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
 * Common interface for backup classes which handle file backups before processing
 */

interface Backup {
    /**
     * Tests, if a file can be backuped
     *
     * @abstract
     * @param string $uri URI of the file to test.
     * @return bool True if file can be backed up, false otherwise
     */
    function canBackup($uri);

    /**
     * Performs file backup operation
     *
     * @abstract
     * @param string $uri URI of the file to backup
     * @return void
     */
    function backup($uri);

    /**
     * Restores last backup for the given file
     *
     * @abstract
     * @param string $uri URI of the file which backup to restore
     * @return void
     */
    function restoreLastBackup($uri);

    /**
     * Deletes last backup for the given file
     *
     * @abstract
     * @param string $uri URI of the file which backup to delete
     * @return void
     */
    function deleteLastBackup($uri);
}
