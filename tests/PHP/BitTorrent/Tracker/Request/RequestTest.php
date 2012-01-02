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
 * @package UnitTests
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 */

namespace PHP\BitTorrent\Tracker\Request;

/**
 * @package UnitTests
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 */
class RequestTest extends \PHPUnit_Framework_TestCase {
    private $query = array(
        'info_hash' => 'aaaaaaaaaaaaaaaaaaaa',
        'peer_id' => 'bbbbbbbbbbbbbbbbbbbb',
        'port' => 123,
        'uploaded' => 123,
        'downloaded' => 234,
        'left' => 345,
        'event' => RequestInterface::EVENT_NONE,
    );

    public function testAccessors() {
        $request = new Request($this->query);

        $this->assertSame($this->query['info_hash'], $request->getInfoHash());
        $this->assertSame('6161616161616161616161616161616161616161', $request->getInfoHashHex());
        $this->assertSame($this->query['peer_id'], $request->getPeerId());
        $this->assertSame($this->query['downloaded'], $request->getDownloaded());
        $this->assertSame($this->query['uploaded'], $request->getUploaded());
        $this->assertSame($this->query['left'], $request->getLeft());
        $this->assertSame($this->query['event'], $request->getEvent());
        $this->assertSame($this->query['port'], $request->getPort());
        $this->assertFalse($request->getNoPeerId());
        $this->assertFalse($request->getCompact());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing query parameter
     */
    public function testRequestWithMissingParameter() {
        $request = new Request();
        $request->validate();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid event: some event
     */
    public function testRequestWithInvalidEvent() {
        $query = $this->query;
        $query['event'] = 'some event';
        $request = new Request($query);
        $request->validate();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid port: 10000
     */
    public function testRequestWithInvalidPort() {
        $query = $this->query;
        $query['port'] = 100000;
        $request = new Request($query);
        $request->validate();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid info hash: value
     */
    public function testRequestWithInvalidInfoHash() {
        $query = $this->query;
        $query['info_hash'] = 'value';
        $request = new Request($query);
        $request->validate();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid peer id: value
     */
    public function testRequestWithInvalidPeerId() {
        $query = $this->query;
        $query['peer_id'] = 'value';
        $request = new Request($query);
        $request->validate();
    }

    public function testGetIpWithIpInQuery() {
        $query = $this->query;
        $query['ip'] = '127.0.0.1';
        $request = new Request($query);
        $this->assertSame($query['ip'], $request->getIp());
    }

    public function testGetIpWithIpInServer() {
        $server = array(
            'HTTP_X_FORWARDED_FOR' => '127.0.0.1',
            'REMOTE_ADDR' => '127.0.0.2',
        );
        $request = new Request($this->query, $server);
        $this->assertSame($server['HTTP_X_FORWARDED_FOR'], $request->getIp());

        unset($server['HTTP_X_FORWARDED_FOR']);

        $request = new Request($this->query, $server);
        $this->assertSame($server['REMOTE_ADDR'], $request->getIp());
    }

    public function testGetIpWithNonePresent() {
        $request = new Request($this->query);
        $this->assertNull($request->getIp());
    }
}
