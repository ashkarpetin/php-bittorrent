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

namespace PHP\BitTorrent\Tracker;

use InvalidArgumentException;

/**
 * @package UnitTests
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 */
class TrackerTest extends \PHPUnit_Framework_TestCase {
    /**
     * Tracker instance
     *
     * @var PHP\BitTorrent\Tracker\Tracker
     */
    private $tracker;

    private $backend;

    /**
     * Set up method
     */
    public function setUp() {
        $this->backend = $this->getMock('PHP\BitTorrent\Tracker\Backend\BackendInterface');
        $this->tracker = new Tracker($this->backend);
    }

    /**
     * Tear down method
     */
    public function tearDown() {
        $this->tracker = null;
    }

    public function testSetAndGetParam() {
        $this->tracker->setParam('key', 'value');
        $this->assertSame('value', $this->tracker->getParam('key'));
    }

    public function testGetParamThatIsNotSet() {
        $this->assertNull($this->tracker->getParam('foobar'));
    }

    public function testSetAndGetParams() {
        $this->tracker->setParams(array('key' => 'value', 'key2' => 'value2'));
        $params = $this->tracker->getParams();
        $this->assertArrayHasKey('key', $params);
        $this->assertArrayHasKey('key2', $params);
        $this->assertSame('value', $params['key']);
        $this->assertSame('value2', $params['key2']);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Invalid request: message
     */
    public function testServeInvalidRequest() {
        $request = $this->getMock('PHP\BitTorrent\Tracker\Request\RequestInterface');
        $request->expects($this->once())->method('validate')->will($this->throwException(new InvalidArgumentException('message')));
        $response = $this->getMock('PHP\BitTorrent\Tracker\Response\ResponseInterface');
        $this->tracker->serve($request, $response);
    }
}
