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
 * Adminhtml crawler edit block
 * @class Maverick_Crawler_Block_Adminhtml_Crawler_Edit
 */

class Maverick_Crawler_Block_Adminhtml_Crawler_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize crawler edit container
     *
     */
    public function __construct()
    {
        /**
         * In parent::_prepareLayout() :
         * $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_controller . '_' . $this->_mode . '_form')
         */
        $this->_blockGroup  = 'maverick_crawler';
        $this->_controller  = 'adminhtml_crawler';

        parent::__construct();

        if ($this->getCrawler()->getId() || (!$this->getCrawler()->getId() && $this->getRequest()->getParam('type'))) {
            $this->_updateButton('save', 'label', Mage::helper('maverick_crawler')->__('Save Crawler'));
            $this->_updateButton('delete', 'label', Mage::helper('maverick_crawler')->__('Delete Crawler'));

            $this->_addButton('saveandcontinue', array(
                'label'     => Mage::helper('maverick_crawler')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class'     => 'save',
            ), -100);

            $this->_formScripts[] = "
                function saveAndContinueEdit(){
                    editForm.submit($('edit_form').action+'back/edit/');
                }
            ";

            if ($this->getCrawler()->getId() &&
               ($this->getCrawler()->getStatus() == Maverick_Crawler_Model_Crawler::STATUS_ENABLED)) {

                $message = Mage::helper('maverick_crawler')->__('Crawling may take several time if you choose lot of pages, Are you sure that you want run it?');
                $this->_addButton('run', array(
                    'label'     => Mage::helper('maverick_crawler')->__('Run Now'),
                    //'onclick'   => 'confirmSetLocation(\''.$message.'\', \'' . $this->getRunUrl() .'\')',
                    'onclick'   => 'window.open(\'' . $this->getRunUrl() .'\')',
                    'class'     => 'delete',
                ), -100);
            }

            $this->setFormActionUrl($this->getUrl('*/*/save', array('_current' => true)));
        } else {
            $this->_removeButton('save');
            $this->_removeButton('delete');
            $this->setFormActionUrl($this->getUrl('*/*/validate'));
        }
    }

    /**
     * Retrieve crawler instance from registry
     *
     * @return Maverick_Crawler_Model_Crawler
     */
    public function getCrawler()
    {
        return Mage::registry('current_crawler');
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getCrawler()->getId()) {
            return Mage::helper('maverick_crawler')->__("Edit Crawler '%s'", $this->escapeHtml($this->getCrawler()->getName()));
        }
        else {
            return Mage::helper('maverick_crawler')->__('New Crawler');
        }
    }

    public function getRunUrl()
    {
        return $this->getUrl('*/*/run', array('id' => $this->getCrawler()->getId()));
    }
}