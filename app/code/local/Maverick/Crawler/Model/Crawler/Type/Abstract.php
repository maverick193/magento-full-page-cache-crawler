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
 * Crawler factory abstract class
 * @class Maverick_Crawler_Model_Crawler_Type_Category
 */

abstract class Maverick_Crawler_Model_Crawler_Type_Abstract
    extends Mage_Core_Model_Abstract implements Maverick_Crawler_Model_Crawler_Type_Interface
{
    protected $_crawler_helper;
    protected $_crawler;
    protected $_nbr_of_visits;

    public function __construct()
    {
        $this->_crawler_helper  = Mage::helper('maverick_crawler/crawler');
        $this->_nbr_of_visits   = Mage::getStoreConfig('crawler/general/clicks') ?
                                  Mage::getStoreConfig('crawler/general/clicks') :
                                  2;
    }

    public function setCrawler(Maverick_Crawler_Model_Crawler $crawler)
    {
        $this->_crawler = $crawler;
    }

    public function getCrawler()
    {
        return $this->_crawler;
    }

    public function getUrls()
    {
        return array();
    }

    public function runUrl($url)
    {
        $crawler = $this->_crawler_helper->visit($url, $this->_nbr_of_visits);
        return $crawler;
    }

    public function scanUrl($crawlObj)
    {
        $url = $this->_crawler_helper->getPageLinks($crawlObj);
        return $url;
    }
}