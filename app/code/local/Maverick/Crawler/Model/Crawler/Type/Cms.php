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
 * CMS page crawler model
 * @class Maverick_Crawler_Model_Crawler_Type_Cms
 */

class Maverick_Crawler_Model_Crawler_Type_Cms extends Maverick_Crawler_Model_Crawler_Type_Abstract
{
    /**
     * Run Crawler
     *
     * @param Maverick_Crawler_Model_Crawler $crawler
     * @param $mode
     * @return array
     */
    public function run(Maverick_Crawler_Model_Crawler $crawler, $mode = Maverick_Crawler_Model_Crawler::MODE_MANUAL)
    {
        parent::run($crawler, $mode);
    }
    /**
     * Retrieve all selected cms pages urls
     *
     * @return array
     */
    public function getUrls()
    {
        $pageIds    = $this->getCrawler()->getPageIds();
        $page       = Mage::getModel('cms/page');
        $helper     = Mage::helper('maverick_crawler');
        $urls       = array();

        foreach ($pageIds as $id) {
            $page->load($id);

            if (!$page->getId()) {
                $helper->log($helper->__('Unable to load cms page with ID %s', $id));
                continue;
            }

            if ($_SERVER['HTTP_HOST']) {
                // to run crawler from admin
                $urls[] = Mage::getModel('core/url')->getUrl($page->getIdentifier());
            } else {
                // to run crawler from shell
                $host = Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_USE_REWRITES) ?
                        Mage::getBaseUrl('web') : Mage::getBaseUrl('web') . 'index.php/';

                $identifier = $page->getIdentifier();
                if (substr($identifier, 0, 1) == '/') {
                    $identifier = substr($identifier, 1);
                }
                $urls[] = $host . $identifier;
            }

            // clear instance data
            $page->setData(array());
            $page->setOrigData();
        }
        return $urls;
    }
}