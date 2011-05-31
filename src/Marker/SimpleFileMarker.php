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

namespace phpImageBatch\Marker;

class SimpleFileMarker implements Marker {

    private $markFilenameSuffix;

    public function __construct($markFilenameSuffix = '.processed') {
        $this->markFilenameSuffix = $markFilenameSuffix;
    }

    public function isMarkedProcessed($uri) {
        $dbFilename = $this->getDbFilenameFor($uri);
        if (file_exists($dbFilename) and filemtime($dbFilename) === filemtime($uri)) {
            return true;
        }
        return false;
    }

    public function markProcessed($uri) {
        if (!touch($this->getDbFilenameFor($uri), filemtime($uri))) {
            throw new MarkerException(sprintf('Cannot mark file "%s" stamped. Permissions problem?', $uri));
        }
    }

    private function getDbFilenameFor($filename) {
        return $filename . $this->markFilenameSuffix;
    }

    function canMarkProcessed($uri) {
        return is_writable(dirname($this->getDbFilenameFor($uri)));
    }

    function unmarkProcessed($uri) {
        if (!unlink($this->getDbFilenameFor($uri))) {
            throw new MarkerException(sprintf('Cannot mark file "%s" unstamped. Permissions problem?', $uri));
        }
    }
}
