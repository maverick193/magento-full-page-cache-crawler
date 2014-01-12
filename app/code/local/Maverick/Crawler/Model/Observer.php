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
 * Cache crawler observer model
 * @class Maverick_Crawler_Model_Observer
 */

class Maverick_Crawler_Model_Observer
{
    /**
     * Set assigned category IDs array to product
     *
     * @param Varien_Event_Observer $observer
     */
    public function setCategoryIds(Varien_Event_Observer $observer)
    {
        if (!Mage::app()->getStore()->isAdmin()) {
            return;
        }
        $crawler    = $observer->getEvent()->getObject();
        $ids        = $crawler->getData('category_ids');

        if (is_string($ids)) {
            $ids = explode(',', $ids);
        } elseif (!is_array($ids)) {
            //@todo create log facility
            Mage::throwException(Mage::helper('maverick_crawler')->__('Invalid category IDs.'));
        }
        foreach ($ids as $i => $v) {
            if (empty($v)) {
                unset($ids[$i]);
            }
        }

        $crawler->setData('category_ids', array_unique($ids));
    }

    /**
     * Save crawler category relations
     *
     * @param Varien_Event_Observer $observer
     */
    public function saveCategories(Varien_Event_Observer $observer)
    {
        if (!Mage::app()->getStore()->isAdmin()) {
            return;
        }
        $crawler  = $observer->getEvent()->getObject();

        /**
         * If category ids data is not declared we haven't do manipulations
         */
        if (!$crawler->hasCategoryIds()) {
            return ;
        }

        $crawler->getResource()->saveCategories($crawler);
    }

    public function loadCategories(Varien_Event_Observer $observer)
    {
        $crawler  = $observer->getEvent()->getObject();
        //if ($crawler->hasLoadCategoryIds()) {
            $crawler->getCategoryIds();
        //}
    }
}