<?php
/**
 * Maverick_Crawler Extension
 *
 * NOTICE OF LICENSE
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @version     0.1.0
 * @category    Maverick
 * @package     Maverick_Crawler
 * @author      Mohammed NAHHAS <m.nahhas@live.fr>
 * @copyright   Copyright (c) 2014 Mohammed NAHHAS
 * @licence     OSL - Open Software Licence 3.0
 *
 */

/**
 * Crawler helper
 * @class Maverick_Crawler_Helper_Crawler
 */
require_once(dirname(__FILE__) . '/../../../../../../../../autoload.php');

class Maverick_Crawler_Helper_Crawler extends Mage_Core_Helper_Abstract
{
    protected $_client;
    protected $_already_visited;
    protected $_frontend_routers;

    /**
     * Init client
     */
    public function __construct()
    {
        $this->_client = new Goutte\Client;
        $this->_frontend_routers = $this->_gatherFrontNames();
    }

    public function visit($url, $repeat = 1)
    {
        if (empty($url)) {
            //@todo log
            return false;
        }

        if (!$this->_validateHref($url)) {
            $message = Mage::helper('maverick_crawler')->__('--> ### Invalid URL : %s', $url);
            Mage::helper('maverick_crawler')->log($message);
            return $message;
        }

        //@todo test visited Urls
        $urlHash = md5($url);
        if (isset($this->_already_visited[$urlHash])) {
            $message = $this->__('--> ### Url Already Visited %s', $url);
            Mage::helper('maverick_crawler')->log($message);
            return $message;
        }

        for ($i=0; $i<$repeat; $i++) {
            $crawler = $this->_client->request('GET', $url);
        }

        $this->_already_visited[$urlHash] = true;
        return $crawler;
    }

    public function getPageLinks($crawler)
    {
        $data       = array();
        $links      = $crawler->filter('a');

        foreach ($links as $index => $link) {
            $href = $link->getAttribute('href');
            if (!in_array($href, $data) && $this->_validateHref($href)) {
                $data[] = $href;
            }
        }

        return $data;
    }

    protected function _validateHref($url)
    {
        // check if is a valid url
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL) ||
            !preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url)
        ) {
            return false;
        }

        // check if url contains a module frontName
        if (0 < count(array_intersect(array_map('strtolower', explode('/', $url)), $this->_frontend_routers))) {
            return false;
        }

        // check if url is an external one (twitter, facebook, trustpilot, ...)
        $urlParsed =  parse_url($url);
        if ($_SERVER['HTTP_HOST'] && (!isset($urlParsed['host']) || $urlParsed['host'] != $_SERVER['HTTP_HOST'])) {
            return false;
        }

        if (!$_SERVER['HTTP_HOST'] && (!isset($urlParsed['host']) || (strpos(Mage::getBaseUrl('web'), $urlParsed['host']) === false))) {
            return false;
        }

        return true;
    }

    protected function _gatherFrontNames()
    {
        $routers    = Mage::app()->getConfig()->getNode('frontend/routers');
        $frontNames = array();

        foreach ($routers->children() as $router) {
            if (!$router->args || !$router->args->frontName) {
                continue;
            }
            $frontNames[] = (string)$router->args->frontName;
        }

        return $frontNames;
    }
}
