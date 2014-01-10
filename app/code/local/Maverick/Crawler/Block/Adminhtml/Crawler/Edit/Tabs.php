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
 * Crawler edit tabs
 * @class Maverick_Crawler_Block_Adminhtml_Crawler_Edit_Tabs
 */

class Maverick_Crawler_Block_Adminhtml_Crawler_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize edit tabs
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('crawler_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('maverick_crawler')->__('Crawler Information'));
    }

    protected function _beforeToHtml()
    {
        if ($this->getProduct()->getId() || (!$this->getProduct()->getId() && $this->getRequest()->getParam('type'))) {
            $this->addTab('main', array(
                'label'     => Mage::helper('maverick_crawler')->__('General Information'),
                'title'     => Mage::helper('maverick_crawler')->__('General Information'),
                'content'   => $this->getLayout()->createBlock('maverick_crawler/adminhtml_crawler_edit_tab_main')->toHtml(),
                'active'    => true
            ));
        } else {
            $this->addTab('set', array(
                'label'     => Mage::helper('maverick_crawler')->__('Settings'),
                'title'     => Mage::helper('maverick_crawler')->__('Settings'),
                'content'   => $this->getLayout()->createBlock('maverick_crawler/adminhtml_crawler_edit_tab_settings')->toHtml(),
                'active'    => true
            ));
        }

        return parent::_beforeToHtml();
    }

    /**
     * Retrive crawler object from block object if not from registry
     *
     * @return Maverick_Crawler_Model_Crawler
     */
    public function getProduct()
    {
        if (!($this->getData('crawler') instanceof Maverick_Crawler_Model_Crawler)) {
            $this->setData('crawler', Mage::registry('current_crawler'));
        }
        return $this->getData('crawler');
    }
}
