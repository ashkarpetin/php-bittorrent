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
 * @package Backend
 * @subpackage Interfaces
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/php-bittorrent
 */

namespace PHP\BitTorrent\Tracker\Backend;

use PHP\BitTorrent\Tracker\Peer\PeerInterface as Peer;

/**
 * Backend interface
 *
 * @package Backend
 * @subpackage Interfaces
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/php-bittorrent
 */
interface BackendInterface {
    /**
     * Check if a torrent with a given info hash exists
     *
     * @param string $infoHash The info hash to look for
     * @return boolean
     */
    function torrentExists($infoHash);

    /**
     * Register an info hash
     *
     * @param string $infoHash The info hash to register
     * @return boolean
     */
    function registerTorrent($infoHash);

    /**
     * Register a torrent peer
     *
     * @param string $infoHash The info hash of the torrent
     * @param PHP\BitTorrent\Tracker\Peer\PeerInterface $peer A peer instance
     * @return boolean
     */
    function registerTorrentPeer($infoHash, Peer $peer);

    /**
     * Check if a peer for a given torrent exists
     *
     * @param string $infoHash The info hash of the torrent
     * @param PHP\BitTorrent\Tracker\Peer\PeerInterface $peer A peer instance
     * @return boolean
     */
    function torrentPeerExists($infoHash, Peer $peer);

    /**
     * Remove a peer from a torrent
     *
     * @param string $infoHash The info hash of the torrent
     * @param PHP\BitTorrent\Tracker\Peer\PeerInterface $peer A peer instance
     * @return boolean
     */
    function deleteTorrentPeer($infoHash, Peer $peer);

    /**
     * Mark a torrent as complete
     *
     * @param string $infoHash The info hash of the torrent
     * @param PHP\BitTorrent\Tracker\Peer\PeerInterface $peer A peer instance
     * @return boolean
     */
    function torrentComplete($infoHash, Peer $peer);

    /**
     * Update information about a torrent peer
     *
     * @param string $infoHash The info hash of the torrent
     * @param PHP\BitTorrent\Tracker\Peer\PeerInterface $peer A peer instance
     * @return boolean
     */
    function updateTorrentPeer($infoHash, Peer $peer);

    /**
     * Get peers connected to a torrent
     *
     * @param string $infoHash The info hash of the torrent
     * @param int $limit Number of peers to return
     * @param PHP\BitTorrent\Tracker\Peer\PeerInterface $exclude A peer instance to exclude from
     *                                                           the list
     * @return PHP\BitTorrent\Tracker\Peer\PeerInterface[]
     */
    function getTorrentPeers($infoHash, $limit = null, Peer $exclude = null);
}
