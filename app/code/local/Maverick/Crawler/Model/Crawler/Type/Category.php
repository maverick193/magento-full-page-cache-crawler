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
     * Run Crawler
     *
     * @param Maverick_Crawler_Model_Crawler $crawler
     * @param $mode
     * @return array
     */
    public function run(Maverick_Crawler_Model_Crawler $crawler, $mode = Maverick_Crawler_Model_Crawler::MODE_MANUAL)
    {
        return parent::run($crawler, $mode);
    }

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

            if (isset($_SERVER['HTTP_HOST'])) {
                // to run crawler from admin
                $urls[] = $category->getUrl();
            } else {
                // to run crawler from shell
                $host = Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_USE_REWRITES) ?
                        Mage::getBaseUrl('web') : Mage::getBaseUrl('web') . 'index.php/';

                $categoryUrlPath = $this->_formatCategoryUrlPath($category->getUrlPath());
                $urls[] = $host . $categoryUrlPath;
            }

            // clear instance data
            $category->setData(array());
            $category->setOrigData();
        }
        return $urls;
    }

    protected function _formatCategoryUrlPath($urlPath)
    {
        if (empty($urlPath)) {
            return '';
        }

        if (substr($urlPath, 0, 1) == '/') {
            $urlPath = substr($urlPath, 1);
        }

        if (substr($urlPath, -5) != '.html') {
            $urlPath .= '.html';
        }

        return $urlPath;
    }
}