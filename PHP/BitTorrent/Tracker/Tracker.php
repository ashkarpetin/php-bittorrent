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
 * @link https://github.com/christeredvartsen/php-bittorrent
 */

namespace PHP\BitTorrent\Tracker;

use PHP\BitTorrent\Tracker\Request\RequestInterface,
    PHP\BitTorrent\Tracker\Response\ResponseInterface,
    PHP\BitTorrent\Tracker\Backend\BackendInterface,
    PHP\BitTorrent\Tracker\Peer\Peer,
    PHP\BitTorrent\Tracker\EventManager\EventManager,
    InvalidArgumentException,
    RuntimeException;

/**
 * BitTorrent tracker
 *
 * @package Tracker
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/php-bittorrent
 */
class Tracker {
    /**
     * A backend
     *
     * @var PHP\BitTorrent\Tracker\Backend\BackendInterface
     */
    private $backend;

    /**
     * Parameters
     *
     * @var array
     */
    private $params = array(
        // The interval used by BitTorrent clients to decide on how often to fetch new peers
        'interval' => 3600,

        // Automatically register all torrents requested
        'autoRegister' => false,

        // Max. number of peers to give on a request
        'maxGive' => 200,
    );

    /**
     * An event manager
     *
     * @var PHP\BitTorrent\Tracker\EventManager\EventManagerInterface
     */
    private $eventManager;

    /**
     * Class constructor
     *
     * @param PHP\BitTorrent\Tracker\Backend\BackendInterface $backend The backend to use
     * @param array $params Parameters for the tracker
     */
    public function __construct(BackendInterface $backend, array $params = array()) {
        $this->setBackend($backend);

        if ($params) {
            $this->setParams($params);
        }
    }

    /**
     * Set the event handler
     *
     * @param PHP\BitTorrent\Tracker\EventManager\EventManagerInterface
     * @return PHP\BitTorrent\Tracker\Tracker
     */
    public function setEventManager(EventManager $eventManager) {
        $this->eventManager = $eventManager;

        return $this;
    }

    /**
     * Set the backend
     *
     * @param PHP\BitTorrent\Tracker\Backend\BackendInterface $backend The backend to use
     * @return PHP\BitTorrent\Tracker\Tracker
     */
    public function setBackend(BackendInterface $backend) {
        $this->backend = $backend;

        return $this;
    }

    /**
     * Get a single value from the params
     *
     * @param string $key The key to get
     * @return mixed
     */
    public function getParam($key) {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }

        return null;
    }

    /**
     * Get the parameters
     *
     * @return array
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * Set a single value in the params array
     *
     * @param string $key The key to set
     * @param mixed $value The value to set
     * @return PHP\BitTorrent\Tracker\Tracker
     */
    public function setParam($key, $value) {
        $this->params[$key] = $value;

        return $this;
    }

    /**
     * Set the params array
     *
     * @param array $params The parameters to set
     * @return PHP\BitTorrent\Tracker\Tracker
     */
    public function setParams(array $params) {
        foreach ($params as $key => $value) {
            $this->setParam($key, $value);
        }

        return $this;
    }

    /**
     * Handle a request
     *
     * @param PHP\BitTorrent\Tracker\Request\RequestInterface $request Request from the client
     * @param PHP\BitTorrent\Tracker\Response\ResponseInterface $response The response to the client
     * @throws PHP\BitTorrent\Tracker\Exception
     */
    public function serve(RequestInterface $request, ResponseInterface $response) {
        try {
            // Validate the request
            $request->validate();
        } catch (InvalidArgumentException $e) {
            throw new Exception('Invalid request: ' . $e->getMessage());
        }

        // See if the torrent exists
        $infoHash = $request->getInfoHash();

        if ($this->backend->torrentExists($infoHash) !== true) {
            // Do we want to automatically register the torrent?
            if ($this->getParam('autoRegister')) {
                $this->backend->registerTorrent($infoHash);
            } else {
                throw new Exception('Torrent not found on this tracker');
            }
        }

        // Create a peer object based on the request to represent the client making the request
        $peer = new Peer();
        $peer->setIp($request->getIp())
             ->setId($request->getPeerId())
             ->setPort($request->getPort())
             ->setDownloaded($request->getDownloaded())
             ->setUploaded($request->getUploaded())
             ->setHasLeft($request->getLeft());

        // See if the peer exists
        $peerExists = $this->backend->torrentPeerExists($infoHash, $peer);

        $event = $request->getEvent();

        if ($event === RequestInterface::EVENT_STOPPED && $peerExists) {
            // If 'stopped' the client has stopped the torrent. If info about the peer exist, delete the peer
            $this->backend->deleteTorrentPeer($infoHash, $peer);
        } else if ($event === RequestInterface::EVENT_COMPLETED && $peerExists) {
            // If 'completed' the user has downloaded the file
            $this->backend->torrentComplete($infoHash, $peer);
        } else if($event === RequestInterface::EVENT_STARTED){
            // If 'started' the client has just started the download. The peer does not exist yet
            $this->backend->registerTorrentPeer($infoHash, $peer);
        } else {
            if ($peerExists) {
                // Just a regular update
                $this->backend->updateTorrentPeer($infoHash, $peer);
            } else {
                // Invalid event
                throw new Exception('Unexpected error');
            }
        }

        // Max. number of torrent peers to give
        $maxGive = (int) $this->getParam('maxGive');

        // Fetch the peers for this torrent (excluding the current one)
        $allPeers = $this->backend->getTorrentPeers($infoHash, $maxGive, $peer);

        // Force usage of the maxGive param in case the backend ignores it
        $allPeers = array_slice($allPeers, 0, $maxGive);

        // Add the peers and the update interval to the response
        $response->addPeers($allPeers)
                 ->setInterval((int) $this->getParam('interval'));

        // Handle some extra options
        if ($request->getNoPeerId()) {
            $response->setNoPeerId(true);
        }

        if ($request->getCompact()) {
            $response->setCompact(true);
        }
    }
}
