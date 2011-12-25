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
 * @package Request
 * @subpackage Tracker
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 */

namespace PHP\BitTorrent\Tracker\Request;

use InvalidArgumentException;

/**
 * Class representing a request from a BitTorrent client
 *
 * @package Request
 * @subpackage Tracker
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 */
class Request implements RequestInterface {
    /**#@+
      * Request names that matches the names used in a typical GET request
      *
      * @var string
      */
     const INFO_HASH     = 'info_hash';
     const INFO_HASH_HEX = 'info_hash_hex';
     const PEER_ID       = 'peer_id';
     const PORT          = 'port';
     const DOWNLOADED    = 'downloaded';
     const UPLOADED      = 'uploaded';
     const LEFT          = 'left';
     const IP            = 'ip';
     const USER_AGENT    = 'user_agent';
     const EVENT         = 'event';
     /**#@-*/

    /**
     * GET data
     *
     * @var array
     */
    private $query;

    /**
     * Server data
     *
     * @var array
     */
    private $server;

    /**
     * Required query parameters
     *
     * @var array
     */
    static protected $requiredQueryParams = array(
        'info_hash',
        'peer_id',
        'port',
        'uploaded',
        'downloaded',
        'left',
    );

    /**
     * Class constructor
     *
     * @param array $query Data from $_GET
     * @param array $server Data from $_SERVER
     */
    public function __construct(array $query = array(), array $server = array()) {
        $this->query = $query;
        $this->server = $server;

        $this->validate();
    }

    /**
     * @see PHP\BitTorrent\Tracker\Request\RequestInterface::validate()
     */
    public function validate() {
        foreach (static::$requiredQueryParams as $key) {
            if (!isset($this->query[$key])) {
                throw new InvalidArgumentException('Missing query parameter: ' . $key);
            }
        }

        $this->validateEvent($this->getEvent());
        $this->validatePort($this->getPort());
        $this->validateInfoHash($this->getInfoHash());
        $this->validatePeerId($this->getPeerId());
    }

    /**
     * Validate the event from the client
     *
     * @param string $event The event from the client
     * @throws InvalidArgumentException
     */
    private function validateEvent($event) {
        // Make sure the event is valid
        switch ($event) {
            case RequestInterface::EVENT_NONE:
            case RequestInterface::EVENT_STARTED:
            case RequestInterface::EVENT_STOPPED:
            case RequestInterface::EVENT_COMPLETED:
                break;
            default:
                throw new InvalidArgumentException('Invalid event: ' . $event);
        }
    }

    /**
     * Validate the port from the client
     *
     * @param int $port The port from the client
     * @throws InvalidArgumentException
     */
    private function validatePort($port) {
        if (!$port || $port > 65535) {
            throw new InvalidArgumentException('Invalid port: ' . $port);
        }
    }

    /**
     * Validate the info hash
     *
     * @param string $infoHash The info hash from the client
     * @throws InvalidArgumentException
     */
    private function validateInfoHash($infoHash) {
        if (strlen($infoHash) !== 20) {
            throw new InvalidArgumentException('Invalid info hash: ' . $infoHash);
        }
    }

    /**
     * Validate the peer id
     *
     * @param string $peerId The peer id from the client
     * @throws InvalidArgumentException
     */
    private function validatePeerId($peerId) {
        if (strlen($peerId) !== 20) {
            throw new InvalidArgumentException('Invalid peer id: ' . $peerId);
        }
    }

    /**
     * @see PHP\BitTorrent\Tracker\Request\RequestInterface::getInfoHash()
     */
    public function getInfoHash() {
        return stripslashes($this->query['info_hash']);
    }

    /**
     * @see PHP\BitTorrent\Tracker\Request\RequestInterface::getInfoHashHex()
     */
    public function getInfoHashHex() {
        return bin2hex($this->getInfoHash());
    }

    /**
     * @see PHP\BitTorrent\Tracker\Request\RequestInterface::getPeerId()
     */
    public function getPeerId() {
        return stripslashes($this->query['peer_id']);
    }

    /**
     * @see PHP\BitTorrent\Tracker\Request\RequestInterface::getIp()
     */
    public function getIp() {
        if (isset($this->query['ip'])) {
            return $this->query['ip'];
        } else if (isset($this->server['HTTP_X_FORWARDED_FOR'])) {
            return $this->server['HTTP_X_FORWARDED_FOR'];
        } else if (isset($this->server['REMOTE_ADDR'])) {
            return $this->server['REMOTE_ADDR'];
        }

        return null;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Request\RequestInterface::getPort()
     */
    public function getPort() {
        return (int) $this->query['port'];
    }

    /**
     * @see PHP\BitTorrent\Tracker\Request\RequestInterface::getDownloaded()
     */
    public function getDownloaded() {
        return (int) $this->query['downloaded'];
    }

    /**
     * @see PHP\BitTorrent\Tracker\Request\RequestInterface::getUploaded()
     */
    public function getUploaded() {
        return (int) $this->query['uploaded'];
    }

    /**
     * @see PHP\BitTorrent\Tracker\Request\RequestInterface::getLeft()
     */
    public function getLeft() {
        return (int) $this->query['left'];
    }

    /**
     * @see PHP\BitTorrent\Tracker\Request\RequestInterface::getEvent()
     */
    public function getEvent() {
        return !empty($this->query['event']) ? $this->query['event'] : '';
    }

    /**
     * @see PHP\BitTorrent\Tracker\Request\RequestInterface::getNoPeerId()
     */
    public function getNoPeerId() {
        return !empty($this->query['nopeer_id']) ? true : false;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Request\RequestInterface::getCompact()
     */
    public function getCompact() {
        return !empty($this->query['compact']) ? true : false;
    }
}
