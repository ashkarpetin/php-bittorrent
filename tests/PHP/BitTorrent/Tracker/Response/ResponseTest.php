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
 * @package UnitTests
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/php-bittorrent
 */

namespace PHP\BitTorrent\Tracker\Response;

use PHP\BitTorrent\Encoder,
    PHP\BitTorrent\Decoder;

/**
 * @package UnitTests
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/php-bittorrent
 */
class ResponseTest extends \PHPUnit_Framework_TestCase {
    /**
     * Response object
     *
     * @var PHP\BitTorrent\Tracker\Response\Response
     */
    private $response;

    public function setUp() {
        $this->response = new Response();
    }

    public function tearDown() {
        $this->response = null;
    }

    public function testAddPeers() {
        $peers = array(
            $this->getMock('PHP\BitTorrent\Tracker\Peer\PeerInterface'),
            $this->getMock('PHP\BitTorrent\Tracker\Peer\PeerInterface'),
            $this->getMock('PHP\BitTorrent\Tracker\Peer\PeerInterface'),
        );
        $this->assertSame($this->response, $this->response->addPeers($peers));
    }

    public function testAddPeer() {
        $peer = $this->getMock('PHP\BitTorrent\Tracker\Peer\PeerInterface');

        $this->assertSame($this->response, $this->response->addPeer($peer));
    }

    public function testSetInterval() {
        $this->assertSame($this->response, $this->response->setInterval(123));
    }

    public function testSetNoPeerId() {
        $this->assertSame($this->response, $this->response->setNoPeerId(false));
    }

    public function testSetCompact() {
        $this->assertSame($this->response, $this->response->setCompact(true));
    }

    public function testAsEncodedString() {
        $encoder = new Encoder();

        $peer = $this->getMock('PHP\BitTorrent\Tracker\Peer\PeerInterface');
        $peer->expects($this->once())->method('getIp')->will($this->returnValue('127.0.0.1'));
        $peer->expects($this->once())->method('getPort')->will($this->returnValue(123));
        $peer->expects($this->once())->method('getId')->will($this->returnValue('id#1'));

        $seed = $this->getMock('PHP\BitTorrent\Tracker\Peer\PeerInterface');
        $seed->expects($this->once())->method('getIp')->will($this->returnValue('127.0.0.1'));
        $seed->expects($this->once())->method('getPort')->will($this->returnValue(1234));
        $seed->expects($this->once())->method('isSeed')->will($this->returnValue(true));
        $seed->expects($this->once())->method('getId')->will($this->returnValue('id#2'));

        $this->response->addPeers(array($peer, $seed));

        $string = $this->response->asEncodedString($encoder);
        $decoder = new Decoder($encoder);
        $decoded = $decoder->decodeDictionary($string);

        $this->assertArrayHasKey('interval', $decoded);
        $this->assertArrayHasKey('complete', $decoded);
        $this->assertArrayHasKey('incomplete', $decoded);
        $this->assertArrayHasKey('peers', $decoded);
        $this->assertInternalType('array', $decoded['peers']);
        $this->assertSame(2, count($decoded['peers']));

        $this->assertArrayHasKey('ip', $decoded['peers'][0]);
        $this->assertArrayHasKey('port', $decoded['peers'][0]);
        $this->assertArrayHasKey('peer id', $decoded['peers'][0]);

        $this->assertArrayHasKey('ip', $decoded['peers'][1]);
        $this->assertArrayHasKey('port', $decoded['peers'][1]);
        $this->assertArrayHasKey('peer id', $decoded['peers'][1]);

        $this->assertSame(1, $decoded['complete']);
        $this->assertSame(1, $decoded['incomplete']);
    }

    public function testAsEncodedStringWithCompactResponse() {
        $encoder = new Encoder();

        $this->response->setCompact(true);

        $peer = $this->getMock('PHP\BitTorrent\Tracker\Peer\PeerInterface');
        $peer->expects($this->once())->method('getIp')->will($this->returnValue('127.0.0.1'));
        $peer->expects($this->once())->method('getPort')->will($this->returnValue(123));

        $seed = $this->getMock('PHP\BitTorrent\Tracker\Peer\PeerInterface');
        $seed->expects($this->once())->method('getIp')->will($this->returnValue('127.0.0.1'));
        $seed->expects($this->once())->method('getPort')->will($this->returnValue(1234));
        $seed->expects($this->once())->method('isSeed')->will($this->returnValue(true));

        $this->response->addPeers(array($peer, $seed));

        $string = $this->response->asEncodedString($encoder);
        $decoder = new Decoder($encoder);
        $decoded = $decoder->decodeDictionary($string);

        $this->assertArrayHasKey('interval', $decoded);
        $this->assertArrayHasKey('complete', $decoded);
        $this->assertArrayHasKey('incomplete', $decoded);
        $this->assertArrayHasKey('peers', $decoded);
        $this->assertInternalType('string', $decoded['peers']);

        $this->assertSame(1, $decoded['complete']);
        $this->assertSame(1, $decoded['incomplete']);
    }
}
