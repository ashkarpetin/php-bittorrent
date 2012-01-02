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
 * @package Interfaces
 * @subpackage Tracker\Response
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/php-bittorrent
 */

namespace PHP\BitTorrent\Tracker\Response;

use PHP\BitTorrent\Tracker\Peer\PeerInterface as Peer,
    PHP\BitTorrent\Encoder;

/**
 * Response interface
 *
 * @package Interfaces
 * @subpackage Tracker\Response
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/php-bittorrent
 */
interface ResponseInterface {
    /**
     * Add an array of peers to the response
     *
     * @param PHP\BitTorrent\Tracker\Peer\PeerInterface[] $peers An array of peer instances
     * @return PHP\BitTorrent\Tracker\Response\ResponseInterface
     */
    function addPeers(array $peers);

    /**
     * Add a peer to the list of peers in the response
     *
     * @param PHP\BitTorrent\Tracker\Peer\PeerInterface $peer A peer instance
     * @return PHP\BitTorrent\Tracker\Response\ResponseInterface
     */
    function addPeer(Peer $peer);

    /**
     * Set the interval
     *
     * @param int $interval The interval to set
     * @return PHP\BitTorrent\Tracker\Response\ResponseInterface
     */
    function setInterval($interval);

    /**
     * Set the nopeer_id flag
     *
     * @param boolean $flag True or false
     * @return PHP\BitTorrent\Tracker\Response\ResponseInterface
     */
    function setNoPeerId($flag);

    /**
     * Set the compact flag
     *
     * @param boolean $flag True of false
     * @return PHP\BitTorrent\Tracker\Response\ResponseInterface
     */
    function setCompact($flag);

    /**
     * Return the complete response in a BitTorrent compliant format
     *
     * @param PHP\BitTorrent\Encoder $encoder The encoder to use when encoding the response
     * @return string
     */
    function asEncodedString(Encoder $encoder);
}
