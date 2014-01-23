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

    /**
     * Run Crawler
     * @todo Cleanup code and code review
     *
     * @param Maverick_Crawler_Model_Crawler $crawler
     * @param $mode
     * @return array
     */
    public function run(Maverick_Crawler_Model_Crawler $crawler, $mode = Maverick_Crawler_Model_Crawler::MODE_MANUAL)
    {
        $errors     = array();
        $urls       = $this->getUrls();
        $logEnabled = Mage::getStoreConfig('crawler/general/log_url');
        $helper     = Mage::helper('maverick_crawler');

        $maxTime    = Mage::getStoreConfig('crawler/general/max_time');
        $startTime  = Mage::getStoreConfigFlag('crawler/general/max_time') ? time() : false;

        // Log Start
        if ($logEnabled) {
            $helper->log($helper->__('###### Starting Crawler ID %s ######', $crawler->getId()));
        }

        foreach ($urls as $url) {
            $maxTimeExceeded = $startTime && ($maxTime < (time() - $startTime));
            if ($maxTimeExceeded) {
                $errMessage = Mage::helper('maverick_crawler')->__('--> ### Stoping Crawler, Maximum Time Of Crawling Exceeded (%s secondes)', $maxTime);
                if ($logEnabled) {
                    Mage::helper('maverick_crawler')->log($errMessage);
                }

                $errors[] = $errMessage;
                break;
            }

            if ($logEnabled) {
                $helper->log($helper->__('--> Warming Up %s (%s time(s))', $url, $this->_nbr_of_visits));
            }
            $crawlerObj = $this->_crawler_helper->visit($url, $this->_nbr_of_visits);

            if (!is_object($crawlerObj)) {
                $errors[] = Mage::helper('maverick_crawler')->__('Some errors encountered while crawling, check your log file');
                continue;
            }

            if ($crawler->getScan() == '1') {
                $pageLinks = $this->_crawler_helper->getPageLinks($crawlerObj);
                if ($logEnabled) {
                    $helper->log($helper->__('--> Scan Option is enabled, scanning %s', $url));
                    $helper->log($helper->__('--> Scan Option found %s urls', count($pageLinks)));
                    $helper->log($helper->__('--> Crawling Them ...'));
                }
                foreach ($pageLinks as $link) {
                    $maxTimeExceeded = $startTime && ($maxTime < (time() - $startTime));
                    if ($maxTimeExceeded) {
                        $errMessage = Mage::helper('maverick_crawler')->__('--> ### Stoping Scanning, Maximum Time Of Crawling Exceeded (%s secondes)', $maxTime);
                        if ($logEnabled) {
                            Mage::helper('maverick_crawler')->log($errMessage);
                        }

                        $errors[] = $errMessage;
                        break;
                    }
                    $start  = time();
                    $this->_crawler_helper->visit($link, $this->_nbr_of_visits);
                    $end    = time() - $start;
                    if ($logEnabled) {
                        $helper->log(
                            $helper->__('    --> Warming Up %s (%s time(s), Took %s secondes)', $link, $this->_nbr_of_visits, $end)
                        );
                    }
                }
            }
        }

        if ($logEnabled) {
            $helper->log($helper->__('###### End Process Crawler ID %s ######', $crawler->getId()));
        }

        $crawler->setLastExecutionMode($mode)
            ->setLastExecutionAt(Mage::getModel('core/date')->date('Y-m-d H:i:s'))
            ->save();

        return $errors;
    }
}