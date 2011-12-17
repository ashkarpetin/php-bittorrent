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
 * @package Tracker
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 */

namespace PHP\BitTorrent\Tracker;

/**
 * This class represents a peer that is connected to the BitTorrent tracker
 *
 * @package Tracker
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 */
class Peer {
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
    private $left;

    /**
     * Set the ip property
     *
     * @param string $ip The ip address to set
     * @return PHP\BitTorrent\Tracker\Peer
     */
    public function setIp($ip) {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get the ip
     *
     * @return string
     */
    public function getIp() {
        return $this->ip;
    }

    /**
     * Set the peer ID
     *
     * @param string $id The ID of the peer
     * @return PHP\BitTorrent\Tracker\Peer
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the peer ID
     *
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set the port number
     *
     * @param int $port The port the peer uses
     * @return PHP\BitTorrent\Tracker\Peer
     */
    public function setPort($port) {
        $this->port = (int) $port;

        return $this;
    }

    /**
     * Get the port
     *
     * @return int
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * Set the downloaded property
     *
     * @param int $downloaded How much has the peer has downloaded
     * @return PHP\BitTorrent\Tracker\Peer
     */
    public function setDownloaded($downloaded) {
        $this->downloaded = (int) $downloaded;

        return $this;
    }

    /**
     * Get the downloaded property
     *
     * @return int
     */
    public function getDownloaded() {
        return $this->downloaded;
    }

    /**
     * Set the uploaded property
     *
     * @param int $uploaded How much the peer has uploaded
     * @return PHP\BitTorrent\Tracker\Peer
     */
    public function setUploaded($uploaded) {
        $this->uploaded = (int) $uploaded;

        return $this;
    }

    /**
     * Get the uploaded property
     *
     * @return int
     */
    public function getUploaded() {
        return $this->uploaded;
    }

    /**
     * Set the left property
     *
     * @param int $left How much the peer has left to download
     * @return PHP\BitTorrent\Tracker\Peer
     */
    public function setLeft($left) {
        $this->left = (int) $left;

        return $this;
    }

    /**
     * Get the left property
     *
     * @return int
     */
    public function getLeft() {
        return $this->left;
    }

    /**
     * See if the peer is a seed
     *
     * If the left property is (int) 0, the peer is a seed
     *
     * @return boolean
     */
    public function isSeed() {
        return ($this->left === 0);
    }

    /**
     * See if the peer is connectable by making a connection to it on the ip:port it provides.
     *
     * @return boolean
     */
    public function isConnectable() {
        $errno  = null;
        $errstr = null;

        $sp = @fsockopen($this->getIp(), $this->getPort(), $errno, $errstr);

        if (!$sp) {
            return false;
        }

        fclose($sp);

        return true;
    }
}
