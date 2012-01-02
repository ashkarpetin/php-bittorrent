<?php
/**
 * PHP BitTorrent
 *
 * Copyright (c) 2011-2012 Christer Edvartsen <cogo@starzinger.net>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * * The above copyright notice and this permission notice shall be included in
 *   all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @package Request
 * @subpackage Interfaces
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/php-bittorrent
 */

namespace PHP\BitTorrent\Tracker\Request;

/**
 * Request interface
 *
 * @package Request
 * @subpackage Interfaces
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/php-bittorrent
 */
interface RequestInterface {
    /**#@+
     * Event types the client can send
     *
     * @var string
     */
    const EVENT_STARTED   = 'started';
    const EVENT_COMPLETED = 'completed';
    const EVENT_STOPPED   = 'stopped';
    const EVENT_NONE      = '';
    /**#@-*/

    /**
     * Check if the data in the request are valid or not
     *
     * @throws InvalidArgumentException
     */
    function validate();

    /**
     * Get the info hash
     *
     * @return string
     */
    function getInfoHash();

    /**
     * Get a hexadecimal version of the info hash
     *
     * @return string
     */
    function getInfoHashHex();

    /**
     * Get the peer id
     *
     * @return string
     */
    function getPeerId();

    /**
     * Get the IP address
     *
     * @return string
     */
    function getIp();

    /**
     * Get the port the client uses
     *
     * @return int
     */
    function getPort();

    /**
     * Get the amount of bytes the client has downloaded
     *
     * @return int
     */
    function getDownloaded();

    /**
     * Get the amount of bytes the client has uploaded
     *
     * @return int
     */
    function getUploaded();

    /**
     * Get the amount of bytes the client has yet to download
     *
     * @return int
     */
    function getLeft();

    /**
     * Get the event the client is triggering
     *
     * This method must return one of the defined event constants.
     *
     * @return string
     */
    function getEvent();

    /**
     * Get the nopeer_id property from the client
     *
     * @param boolean
     */
    function getNoPeerId();

    /**
     * Get the compact property from the client
     *
     * @param boolean
     */
    function getCompact();

    /**
     * Get the request headers
     *
     * @return PHP\BitTorrent\Tracker\Request\HeaderContainer
     */
    function getHeaders();
}
