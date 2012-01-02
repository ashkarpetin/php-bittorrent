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
 * @package Peer
 * @subpackage Tracker
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/php-bittorrent
 */

namespace PHP\BitTorrent\Tracker\Peer;

use InvalidArgumentException;

/**
 * This class represents a peer that is connected to the BitTorrent tracker
 *
 * @package Peer
 * @subpackage Tracker
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/php-bittorrent
 */
class Peer implements PeerInterface {
    /**
     * Ip address of the peer
     *
     * @var string
     */
    private $ip;

    /**
     * ID of the peer
     *
     * @var string
     */
    private $id;

    /**
     * Port number the peer uses
     *
     * @var int
     */
    private $port;

    /**
     * Number of bytes the peer has downloaded
     *
     * @var int
     */
    private $downloaded;

    /**
     * Number of bytes the peer has uploaded
     *
     * @var int
     */
    private $uploaded;

    /**
     * Number of bytes the peer has left to download
     *
     * @var int
     */
    private $hasLeft;

    /**
     * @see PHP\BitTorrent\Tracker\Peer\PeerInterface::setIp()
     */
    public function setIp($ip) {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Peer\PeerInterface::getIp()
     */
    public function getIp() {
        return $this->ip;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Peer\PeerInterface::setId()
     */
    public function setId($id) {
        $id = $this->escape($id);

        if (strlen($id) !== 20) {
            throw new InvalidArgumentException('Invalid peer id: ' . $id);
        }

        $this->id = $id;

        return $this;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Peer\PeerInterface::getId()
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Peer\PeerInterface::setPort()
     */
    public function setPort($port) {
        $port = (int) $port;

        if (!$port || $port > 65535) {
            throw new InvalidArgumentException('Invalid port: ' . $port);
        }

        $this->port = $port;

        return $this;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Peer\PeerInterface::getPort()
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Peer\PeerInterface::setDownloaded()
     */
    public function setDownloaded($downloaded) {
        $this->downloaded = (int) $downloaded;

        return $this;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Peer\PeerInterface::getDownloaded()
     */
    public function getDownloaded() {
        return $this->downloaded;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Peer\PeerInterface::setUploaded()
     */
    public function setUploaded($uploaded) {
        $this->uploaded = (int) $uploaded;

        return $this;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Peer\PeerInterface::getUploaded()
     */
    public function getUploaded() {
        return $this->uploaded;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Peer\PeerInterface::setHasLeft()
     */
    public function setHasLeft($hasLeft) {
        $this->hasLeft = (int) $hasLeft;

        return $this;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Peer\PeerInterface::getHasLeft()
     */
    public function getHasLeft() {
        return $this->hasLeft;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Peer\PeerInterface::isSeed()
     */
    public function isSeed() {
        return ($this->hasLeft === 0);
    }

    /**
     * @see PHP\BitTorrent\Tracker\Peer\PeerInterface::isConnectable()
     */
    public function isConnectable() {
        $errno  = null;
        $errstr = null;

        set_error_handler(function() { return true; });
        $sp = fsockopen($this->getIp(), $this->getPort(), $errno, $errstr);
        restore_error_handler();

        if (!$sp) {
            return false;
        }

        fclose($sp);

        return true;
    }

    /**
     * Escape data from the request
     *
     * @param string $data Data to escape
     * @return string
     */
    private function escape($data) {
        return stripslashes($data);
    }
}
