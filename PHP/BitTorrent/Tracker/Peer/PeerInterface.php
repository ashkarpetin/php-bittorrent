<?php
/**
 * PHP BitTorrent
 *
 * Copyright (c) 2011 Christer Edvartsen <cogo@starzinger.net>
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
 * @package Peer
 * @subpackage Interfaces
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/php-bittorrent
 */

namespace PHP\BitTorrent\Tracker\Peer;

/**
 * Peer interface
 *
 * @package Peer
 * @subpackage Interfaces
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/php-bittorrent
 */
interface PeerInterface {
    /**
     * Set the peer id
     *
     * @param string $id The id to set
     * @return PHP\BitTorrent\Tracker\Peer\PeerInterface
     * @throws InvalidArgumentException
     */
    function setId($id);

    /**
     * Get the peer id
     *
     * @return string
     */
    function getId();

    /**
     * Set the peer ip address
     *
     * @param string $ip The ip address to set
     * @return PHP\BitTorrent\Tracker\Peer\PeerInterface
     */
    function setIp($ip);

    /**
     * Get the ip address
     *
     * @return string
     */
    function getIp();

    /**
     * Set the port the client uses
     *
     * @param int $port The port
     * @return PHP\BitTorrent\Tracker\Peer\PeerInterface
     * @throws InvalidArgumentException
     */
    function setPort($port);

    /**
     * Get the port the client uses
     *
     * @return int
     */
    function getPort();

    /**
     * Set the amount of bytes the peer has downloaded
     *
     * @param int $downloaded Amount of bytes downloaded
     * @return PHP\BitTorrent\Tracker\Peer\PeerInterface
     */
    function setDownloaded($downloaded);

    /**
     * Get the amount of bytes the peer has downloaded
     *
     * @return int
     */
    function getDownloaded();

    /**
     * Set the amount of bytes the peer has uploaded
     *
     * @param int $uploaded Amount of bytes uploaded
     * @return PHP\BitTorrent\Tracker\Peer\PeerInterface
     */
    function setUploaded($uploaded);

    /**
     * Get the amount of bytes the peer has uploaded
     *
     * @return int
     */
    function getUploaded();

    /**
     * Set how much the peer has left to download to become a seed
     *
     * @param int $hasLeft The amount of bytes the peer has left to download
     * @return PHP\BitTorrent\Tracker\Peer\PeerInterface
     */
    function setHasLeft($hasLeft);

    /**
     * Get the amount of bytes the peer has left to download to become a seed
     *
     * @return int
     */
    function getHasLeft();

    /**
     * Check if the peer is a seed or not
     *
     * If the peer has nothing left to download, the peer is a seed
     *
     * @return boolean
     */
    function isSeed();

    /**
     * Check if the peer is connectable or not
     *
     * @return boolean
     */
    function isConnectable();
}
