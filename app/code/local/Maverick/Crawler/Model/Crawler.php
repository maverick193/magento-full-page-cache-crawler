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
 * Cache crawler model
 * @class Maverick_Crawler_Model_Crawler
 */

class Maverick_Crawler_Model_Crawler extends Mage_Core_Model_Abstract
{
    const STATUS_ENABLED    = 1;
    const STATUS_DISABLED   = 0;

    const MODE_MANUAL       = 'Manual';
    const MODE_CRON         = 'Cron';
    const MODE_SHELL        = 'Shell';

    protected $_eventPrefix = 'maverick_crawler';

    /**
     * Initialize crawler model
     */
    function _construct()
    {
        $this->_init('maverick_crawler/crawler');
    }

    /**
     * Processing object before save data
     *
     * @return Maverick_Crawler_Model_Crawler
     */
    protected function _beforeSave()
    {
        if ($type = $this->getType()) {
            Mage::dispatchEvent($type . '_save_before', $this->_getEventData());
        }
        return parent::_beforeSave();
    }

    /**
     * Processing object after save data
     *
     * @return Maverick_Crawler_Model_Crawler
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        if ($type = $this->getType()) {
            Mage::dispatchEvent($type . '_save_after', $this->_getEventData());
        }
        return $this;
    }

    /**
     * Processing object after load data
     *
     * @return Maverick_Crawler_Model_Crawler
     */
    protected function _afterLoad()
    {
        if ($type = $this->getType()) {
            Mage::dispatchEvent($type . '_load_after', $this->_getEventData());
        }
        return parent::_afterLoad();
    }

    /**
     * Retrieve assigned category Ids
     *
     * @return array
     */
    public function getCategoryIds()
    {
        if (!$this->hasData('category_ids')) {
            $ids = $this->_getResource()->getCategoryIds($this);
            $this->setData('category_ids', $ids);
        }

        return (array) $this->_getData('category_ids');
    }

    /**
     * Retrieve assigned page Ids
     *
     * @return array
     */
    public function getPageIds()
    {
        if (!$this->hasData('page_ids')) {
            $ids = $this->_getResource()->getPageIds($this);
            $this->setData('page_ids', $ids);
        }

        return (array) $this->_getData('page_ids');
    }

    protected function _beforeRun()
    {
        // Check crawler type
        if (!$this->getType()) {
            //@todo log
            Mage::throwException(
                Mage::helper('maverick_crawler')->__('Unable to run crawler, type is not found or not defined')
            );
        }

        // Check crawler type object
        $obj = Mage::getSingleton('maverick_crawler/' . $this->getType());
        if (!is_object($obj)) {
            Mage::throwException(
                Mage::helper('maverick_crawler')->__('Unable to run crawler, cannot instantiate crawler type')
            );
        }

        // Check run method exists
        if (!method_exists($obj, 'run')) {
            Mage::throwException(
                Mage::helper('maverick_crawler')->__('Unable to run crawler, run() method does not exist')
            );
        }

        return $obj;
    }

    /**
     * Run Crawler
     *
     * @param string $mode
     * @return array $result
     */
    public function run($mode = self::MODE_MANUAL)
    {
        $obj    = $this->getFactory();
        $result = $obj->run($this, $mode);

        return $result;
    }

    /**
     * Get crawler type instance
     *
     * @return mixed
     */
    public function getFactory()
    {
        $obj = $this->_beforeRun();
        $obj->setCrawler($this);

        return $obj;
    }
}