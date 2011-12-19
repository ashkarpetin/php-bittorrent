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
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 */

namespace PHP\BitTorrent\Tracker\Backend;

use PHP\BitTorrent\Tracker\Peer\PeerInterface,
    PHP\BitTorrent\Tracker\Peer\Peer,
    PDO,
    RuntimeException;

/**
 * @package Backend
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 */
class Sqlite implements BackendInterface {
    /**
     * Database resource
     *
     * @var PDO
     */
    private $db;

    /**
     * Backend parameters
     *
     * @var array
     */
    private $params = array(
        // Name of the database (required)
        'database' => null,
    );

    /**
     * Class constructor
     *
     * @param array $params Parameters for the backend
     */
    public function __construct(array $params = array()) {
        $this->params = array_merge($this->params, $params);
    }

    /**
     * Fetch the database resource
     *
     * @throws RuntimeException
     * @return PDO
     */
    private function getDb() {
        if ($this->db === null) {
            if (empty($this->params['database'])) {
                throw new RuntimeException('Missing "database" parameter');
            }

            try {
                $createTables = false;

                // See if the database exist. If it does, skip the step that creates the tables
                // needed.
                if (!file_exists($database)) {
                    $createTables = true;
                }

                // Get semaphore
                $sem = sem_get(1);

                $this->db = new PDO('sqlite:' . $database);

                // Create tables?
                if ($createTables) {
                    // Acquire semaphore
                    sem_acquire($sem);

                    $sql = "
                        CREATE TABLE peer (
                            torrentId INTEGER NOT NULL default '0',
                            peerId BLOB NOT NULL,
                            ip TEXT NOT NULL,
                            port INTEGER NOT NULL default '0',
                            uploaded INTEGER NOT NULL default '0',
                            downloaded INTEGER NOT NULL default '0',
                            left INTEGER NOT NULL default '0',
                            seeder BOOLEAN NOT NULL default '0',
                            started INTEGER NOT NULL,
                            connectable BOOLEAN NOT NULL default '1',
                            PRIMARY KEY (torrentId,peerId)
                        )
                    ";
                    $this->db->query($sql);

                    $sql = "
                        CREATE TABLE torrent (
                            infoHash BLOB UNIQUE
                        );
                    ";
                    $this->db->query($sql);

                    // Relase semaphore
                    sem_release($sem);
                } else {
                    // Acquire and release semaphore. If another request is currently creating the
                    // tables in the database, this will block until the creation is complete.
                    sem_acquire($sem);
                    sem_release($sem);
                }
            } catch (PDOException $e) {
                throw new RuntimeException('Could not open database: ' . $e->getMessage());
            }
        }

        return $this->db;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Backend\BackendInterface::torrentExists()
     */
    public function torrentExists($infoHash) {
        $sql = "
            SELECT
                _rowid_
            FROM
                torrent
            WHERE
                infoHash = :infoHash
        ";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute(array(':infoHash' => $infoHash));
        $row = $stmt->fetch();

        return !empty($row);
    }

    /**
     * @see PHP\BitTorrent\Tracker\Backend\BackendInterface::torrentPeerExists()
     */
    public function torrentPeerExists($infoHash, Peer $peer) {
        $sql = "
            SELECT
                p.ip
            FROM
                peer p
            LEFT JOIN
                torrent t
            ON
                p.torrentId = t._rowid_
            WHERE
                p.peerId = :peerId AND
                t.infoHash = :infoHash
        ";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute(array(':peerId' => $peer->getId(), ':infoHash' => $infoHash));
        $row = $stmt->fetch();

        return !empty($row);
    }

    /**
     * @see PHP\BitTorrent\Tracker\Backend\BackendInterface::getTorrentPeers()
     */
    public function getTorrentPeers($infoHash, $limit = null, PeerInterface $exclude = null);
        $where = array();
        $where[] = "t.infoHash = :infoHash";

        if ($exclude !== null) {
            $where[] = "p.peerId != :excludePeerId";
        }

        // Initialize limit clause variable
        $limitClause = null;

        if ($limit !== null) {
            $limitClause = " LIMIT " . (int) $limit;
        }

        $sql = "
            SELECT
                p.ip,
                p.peerId,
                p.port,
                p.downloaded,
                p.uploaded,
                p.left
            FROM
                peer p
            LEFT JOIN
                torrent t
            ON
                p.torrentId = t._rowid_
            WHERE
                " . implode(' AND ', $where) .
            $limitClause;

        $stmt = $this->getDb()->prepare($sql);

        $stmt->bindValue(':infoHash', $infoHash);

        if ($exclude !== null) {
            $stmt->bindValue(':excludePeerId', $exclude->getId());
        }

        $stmt->execute();
        $peers = array();

        while ($p = $stmt->fetch()) {
            $peer = new Peer();
            $peer->setIp($p['ip'])
                 ->setId($p['peerId'])
                 ->setPort($p['port'])
                 ->setDownloaded($p['downloaded'])
                 ->setUploaded($p['uploaded'])
                 ->setHasLeft($p['left']);

            $peers[] = $peer;
        }

        return $peers;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Backend\BackendInterface::deleteTorrentPeer()
     */
    public function deleteTorrentPeer($infoHash, PeerInterface $peer) {
        $torrentId = $this->getTorrentId($infoHash);

        $sql = "
            DELETE FROM
                peer
            WHERE
                torrentId = :torrentId AND
                peerId = :peerId
        ";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute(array(':torrentId' => $torrentId, ':peerId' => $peer->getId()));

        return (bool) $stmt->rowCount();
    }

    /**
     * @see PHP\BitTorrent\Tracker\Backend\BackendInterface::registerTorrentPeer()
     */
    public function registerTorrentPeer($infoHash, PeerInterface $peer) {
        $torrentId = $this->getTorrentId($infoHash);

        if (!$torrentId) {
            return false;
        }

        $time = time();

        $sql = "
            INSERT INTO peer (
                torrentId,
                peerId,
                ip,
                port,
                uploaded,
                downloaded,
                left,
                seeder,
                started,
                connectable
            ) VALUES (
                :torrentId,
                :peerId,
                :peerIp,
                :peerPort,
                :peerUploaded,
                :peerDownloaded,
                :peerLeft,
                :peerIsSeed,
                :time,
                :peerIsConnectable
            )
        ";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute(array(
            ':torrentId'         => $torrentId,
            ':peerId'            => $peer->getId(),
            ':peerIp'            => $peer->getIp(),
            ':peerPort'          => $peer->getPort(),
            ':peerUploaded'      => $peer->getUploaded(),
            ':peerDownloaded'    => $peer->getDownloaded(),
            ':peerLeft'          => $peer->getHasLeft(),
            ':peerIsSeed'        => ($peer->isSeed() ? 1 : 0),
            ':time'              => $time,
            ':peerIsConnectable' => ($peer->isConnectable() ? 1 : 0),
        ));

        return true;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Backend\BackendInterface::updateTorrentPeer()
     */
    public function updateTorrentPeer($infoHash, PeerInterface $peer) {
        $torrentId = $this->getTorrentId($infoHash);

        if (!$torrentId) {
            return false;
        }

        // Update information about the peer
        $sql = "
            UPDATE
                peer
            SET
                ip = :peerIp,
                port = :peerPort,
                uploaded = :peerUploaded,
                downloaded = :peerDownloaded,
                left = :peerLeft,
                seeder = :peerIsSeed,
                connectable = :peerIsConnectable
            WHERE
                peerId = :peerId AND
                torrentId = :torrentId
        ";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute(array(
            ':peerIp'            => $peer->getIp(),
            ':peerPort'          => $peer->getPort(),
            ':peerUploaded'      => $peer->getUploaded(),
            ':peerDownloaded'    => $peer->getDownloaded(),
            ':peerLeft'          => $peer->getHasLeft(),
            ':peerIsSeed'        => ($peer->isSeed() ? 1 : 0),
            ':peerIsConnectable' => ($peer->isConnectable() ? 1 : 0),
            ':peerId'            => $peer->getId(),
            ':torrentId'         => $torrentId,
        ));

        return (bool) $stmt->rowCount();
    }

    /**
     * @see PHP\BitTorrent\Tracker\Backend\BackendInterface::torrentComplete()
     */
    public function torrentComplete($infoHash, PeerInterface $peer) {
        if ($this->updateTorrentPeer($infoHash, $peer)) {
            $torrentId = $this->getTorrentId($infoHash);

            // Switch peer to seed
            $sql = "
                UPDATE
                    peer
                SET
                    seeder = 1
                WHERE
                    peerId = :peerId AND
                    torrentId = :torrentId
            ";
            $stmt = $this->getDb()->prepare($sql);

            return $stmt->execute(array(
                ':peerId'    => $peer->getId(),
                ':torrentId' => $torrentId,
            ));
        }

        return false;
    }

    /**
     * @see PHP\BitTorrent\Tracker\Backend\BackendInterface::registerTorrent()
     */
    public function registerTorrent($infoHash) {
        $torrentId = $this->getTorrentId($infoHash);

        // If the torrent already exist, return false
        if ($torrentId) {
            return false;
        }

        $sql = "
            INSERT INTO torrent (
                infoHash
            ) VALUES (
                :infoHash
            )
        ";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute(array(':infoHash' => $infoHash));

        return true;
    }

    /**
     * Get the internal ID of a torrent
     *
     * @param string $infoHash The info hash of the torrent
     * @return int The unique ID stored in the database
     */
    private function getTorrentId($infoHash) {
        $sql = "
            SELECT
                _rowid_
            FROM
                torrent
            WHERE
                infoHash = :infoHash
        ";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute(array(':infoHash' => $infoHash));
        $torrentId = $stmt->fetchColumn();

        $stmt->closeCursor();

        return (int) $torrentId;
    }
}
