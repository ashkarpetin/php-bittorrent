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
 * @subpackage EventManager
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/php-bittorrent
 */

namespace PHP\BitTorrent\Tracker\EventManager;

use PHP\BitTorrent\Tracker\Request\RequestInterface,
    PHP\BitTorrent\Tracker\Response\ResponseInterface;

/**
 * Event class
 *
 * @package Tracker
 * @subpackage EventManager
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/php-bittorrent
 */
class Event implements EventInterface {
    /**
     * Name of the current event
     *
     * @var string
     */
    private $name;

    /**
     * Request instance
     *
     * @var PHP\BitTorrent\Tracker\Request\RequestInterface
     */
    private $request;

    /**
     * Response instance
     *
     * @var PHP\BitTorrent\Tracker\Response\ResponseInterface
     */
    private $response;

    /**
     * Class contsructor
     *
     * @param string $name The name of the current event
     * @param PHP\BitTorrent\Tracker\Request\RequestInterface $request Request instance
     * @param PHP\BitTorrent\Tracker\Response\ResponseInterface $response Response instance
     */
    public function __construct($name, RequestInterface $request, ResponseInterface $response) {
        $this->name = $name;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @see PHP\BitTorrent\Tracker\EventManager\EventInterface::getName()
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @see PHP\BitTorrent\Tracker\EventManager\EventInterface::getRequest()
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * @see PHP\BitTorrent\Tracker\EventManager\EventInterface::getResponse()
     */
    public function getResponse() {
        return $this->response;
    }
}
