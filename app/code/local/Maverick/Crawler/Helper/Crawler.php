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
    protected $_alreadyVisited;
    protected $_frontendRouters;

    /**
     * Init client
     */
    public function __construct()
    {
        $this->_client = new Goutte\Client;
        $this->_frontendRouters = $this->_gatherFrontNames();
    }

    /**
     * Visit url
     *
     * @param $url
     * @param int $repeat
     * @return bool|string|\Symfony\Component\DomCrawler\Crawler
     */
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
        if (isset($this->_alreadyVisited[$urlHash])) {
            $message = $this->__('--> ### Url Already Visited %s', $url);
            Mage::helper('maverick_crawler')->log($message);
            return $message;
        }

        for ($i=0; $i<$repeat; $i++) {
            $crawler = $this->_client->request('GET', $url);
        }

        $this->_alreadyVisited[$urlHash] = true;
        return $crawler;
    }

    /**
     * Retrieve page links
     *
     * @param $crawler
     * @return array
     */
    public function getPageLinks($crawler)
    {
        $data       = array();
        $links      = $crawler->filter('a');

        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            if (!in_array($href, $data) && $this->_validateHref($href)) {
                $data[] = $href;
            }
        }

        return $data;
    }

    /**
     * Validate url
     *
     * @param $url
     * @return bool
     */
    protected function _validateHref($url)
    {
        // check if is a valid url
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL) ||
            !preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url)
        ) {
            return false;
        }

        // check if url contains a module frontName
        if (0 < count(array_intersect(array_map('strtolower', explode('/', $url)), $this->_frontendRouters))) {
            return false;
        }

        // check if url is an external one (twitter, facebook, trustpilot, ...)
        $urlParsed  = parse_url($url);
        $httpHost   = Mage::helper('core/http')->getHttpHost();

        if ($httpHost && (!isset($urlParsed['host']) || $urlParsed['host'] != $httpHost)) {
            return false;
        }

        if (!$httpHost && (!isset($urlParsed['host'])
            || (strpos(Mage::getBaseUrl('web'), $urlParsed['host']) === false))) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve available modules front names
     * @return array
     */
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

    /**
     * Public function to validate url
     * -- Used in crawler console shell --
     *
     * @param $url
     * @return bool
     */
    public function validateUrl($url)
    {
        return $this->_validateHref($url);
    }
}