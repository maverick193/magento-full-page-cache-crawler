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
 * Category crawler model
 * @class Maverick_Crawler_Model_Crawler_Type_Category
 */

class Maverick_Crawler_Model_Crawler_Type_Category extends Maverick_Crawler_Model_Crawler_Type_Abstract
{
    /**
     * Retrieve all selected categories urls
     *
     * @return array
     */
    public function getUrls()
    {
        $categoryIds    = $this->getCrawler()->getCategoryIds();
        $category       = Mage::getModel('catalog/category');
        $helper         = Mage::helper('maverick_crawler');
        $urls           = array();

        foreach ($categoryIds as $id) {
            $category->load($id);

            if (!$category->getId()) {
                $helper->log($helper->__('Unable to load category with ID %s', $id));
                continue;
            }

            if ($_SERVER['HTTP_HOST']) {
                // to run crawler from admin
                $urls[] = $category->getUrl();
            } else {
                // to run crawler from shell
                $urls[] = $category->getUrlPath();
            }

            // clear instance data
            $category->setData(array());
            $category->setOrigData();
        }
        return $urls;
    }

    /**
     * Run Crawler
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

        // Log Start
        if ($logEnabled) {
            $helper->log($helper->__('###### Starting Crawler ID %s ######', $crawler->getId()));
        }

        foreach ($urls as $url) {
            if ($logEnabled) {
                $helper->log($helper->__('--> Warming Up %s (%s time(s))', $url, $this->_nbr_of_visits));
            }
            $crawlerObj = $this->_crawler_helper->visit($url, $this->_nbr_of_visits);

            if (!is_object($crawlerObj)) {
                $errors[0] = Mage::helper('maverick_crawler')->__('Some errors encountered while crawling, check your log file');
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
                    if ($logEnabled) {
                        $helper->log($helper->__('    --> Warming Up %s', $link));
                    }
                    $this->_crawler_helper->visit($link, $this->_nbr_of_visits);
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