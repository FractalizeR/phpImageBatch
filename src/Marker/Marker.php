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

/**
 * Marker is an object that marks files as processes so that there is no need to process them again and again
 */

interface Marker {

    /**
     * Checks if an object on given uri can be marked as processed
     *
     * @abstract
     * @param string $uri Object URI. Usually filename.
     * @return bool True if a given object can be marked as processed
     */
    function canMarkProcessed($uri);

    /**
     * Checks if an object on given uri is marked as processed
     *
     * @abstract
     * @param string $uri Usually filename.
     * @return bool True if a given object is marked as processed
     */
    function isMarkedProcessed($uri);

    /**
     * Marks a given object as processed
     * 
     * @abstract
     * @param string $uri Usually filename.
     * @return void
     */
    function markProcessed($uri);

    /**
     * Removes "processed" mark from a given object
     *
     * @param string $uri Usually filename.
     * @return void
     */
    function unmarkProcessed($uri);
}
