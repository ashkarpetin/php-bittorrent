<?php
/**
 * PHP BitTorrent
 *
 * Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
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
 * @subpackage Response
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/php-bittorrent
 */

namespace PHP\BitTorrent\Tracker\Response;

use PHP\BitTorrent\Tracker\Peer\PeerInterface as Peer,
    PHP\BitTorrent\Encoder;

/**
 * Class representing a response from the tracker
 *
 * @package Tracker
 * @subpackage Response
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/php-bittorrent
 */
class Response implements ResponseInterface {
    /**
     * Interval in the response
     *
     * @var int
     */
    private $interval = 3600;

    /**
     * Peers in the response
     *
     * @var PHP\BitTorrent\Tracker\Peer\PeerInterface[]
     */
    private $peers = array();

    /**
     * Wether or not to include the peer id in the response
     *
     * @var boolean
     */
    private $noPeerId = false;

    /**
     * Wether or not to generate a compact response
     *
     * @var boolean
     */
    private $compact = false;

    /**
     * @see PHP\BitTorrent\Tracker\Response\ResponseInterface::addPeers()
     */
    public function addPeers(array $peers) {
        foreach ($peers as $peer) {
            $this->addPeer($peer);
        }

        return $this;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Response\ResponseInterface::addPeer()
     */
    public function addPeer(Peer $peer) {
        $this->peers[] = $peer;

        return $this;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Response\ResponseInterface::setInterval()
     */
    public function setInterval($interval) {
        $this->interval = (int) $interval;

        return $this;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Response\ResponseInterface::setNoPeerId()
     */
    public function setNoPeerId($flag) {
        $this->noPeerId = (bool) $flag;

        return $this;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Response\ResponseInterface::setCompact()
     */
    public function setCompact($flag) {
        $this->compact = (bool) $flag;

        return $this;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Response\ResponseInterface::asEncodedStrig()
     */
    public function asEncodedString(Encoder $encoder) {
        // Initialize (in)complete variables
        $complete = 0;
        $incomplete = 0;

        if ($this->compact) {
            // Compact response
            $peers = '';

            foreach ($this->peers as $peer) {
                $peers .= pack('Nn', ip2long($peer->getIp()), $peer->getPort());

                if ($peer->isSeed()) {
                    $complete++;
                } else {
                    $incomplete++;
                }
            }
        } else {
            // Regular response
            $peers = array();

            foreach ($this->peers as $peer) {
                $p = array(
                    'ip'   => $peer->getIp(),
                    'port' => $peer->getPort(),
                );

                // Include peer id unless specified otherwise
                if (!$this->noPeerId) {
                    $p['peer id'] = $peer->getId();
                }

                $peers[] = $p;

                if ($peer->isSeed()) {
                    $complete++;
                } else {
                    $incomplete++;
                }
            }
        }

        $response = array(
            'interval'   => $this->interval,
            'complete'   => $complete,
            'incomplete' => $incomplete,
            'peers'      => $peers,
        );

        // Return the encoded the response
        return $encoder->encodeDictionary($response);
    }
}
