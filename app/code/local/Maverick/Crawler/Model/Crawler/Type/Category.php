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

class Maverick_Crawler_Model_Crawler_Type_Category implements Maverick_Crawler_Model_Crawler_Type_Interface
{
    /**
     * Run Crawler
     *
     * @param Maverick_Crawler_Model_Crawler $crawler
     * @return array
     */
    public function run(Maverick_Crawler_Model_Crawler $crawler)
    {
        $errors         = array();
        $categoryIds    = $crawler->getCategoryIds();
        $helper         = Mage::helper('maverick_crawler/crawler');

        if (!$categoryIds) {
            return array();
        }

        $category = Mage::getModel('catalog/category');
        foreach ($categoryIds as $id) {
            $category->load($id);

            if (!$category->getId()) {
                //@todo log
                continue;
            }

            $url = $category->getUrl();
            $helper->visit($url, '2');

            // clear instance data
            $category->setData(array());
            $category->setOrigData();
        }

        return $errors;
    }
}