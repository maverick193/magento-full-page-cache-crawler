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
 * Main tab block
 * @class Maverick_Crawler_Block_Adminhtml_Crawler_Edit_Tab_Main
 */

class Maverick_Crawler_Block_Adminhtml_Crawler_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_crawler_main');

        $crawler = Mage::registry('current_crawler');

        $fieldset = $form->addFieldset('main', array('legend'=>Mage::helper('maverick_crawler')->__('General Information')));

        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('maverick_crawler')->__('Crawler Name'),
            'title'     => Mage::helper('maverick_crawler')->__('Crawler Name'),
            'name'      => 'name',
            'required'  => false
        ));

        $fieldset->addField('hidden_type', 'hidden', array(
            'name'      => 'hidden_type',
            'value'     => $this->_getCrawlerType(),
        ));

        $fieldset->addField('type', 'select', array(
            'label'     => Mage::helper('maverick_crawler')->__('Crawler Type'),
            'title'     => Mage::helper('maverick_crawler')->__('Crawler Type'),
            'name'      => 'type',
            'disabled'  => 'disabled',
            'value'     => $this->_getCrawlerType(),
            'options'   => Mage::getSingleton('maverick_crawler/source_crawler_type')->optionForGrid(),
        ));

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('maverick_crawler')->__('Status'),
            'title'     => Mage::helper('maverick_crawler')->__('Status'),
            'name'      => 'status',
            'required'  => true,
            'options'   => array(
                Maverick_Crawler_Model_Crawler::STATUS_ENABLED  => Mage::helper('maverick_crawler')->__('Enabled'),
                Maverick_Crawler_Model_Crawler::STATUS_DISABLED => Mage::helper('maverick_crawler')->__('Disabled'),
            ),
        ));

        $fieldset->addField('scheduled', 'select', array(
            'label'     => Mage::helper('maverick_crawler')->__('Scheduled'),
            'title'     => Mage::helper('maverick_crawler')->__('Scheduled'),
            'name'      => 'scheduled',
            'required'  => true,
            'options'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
        ));

        $form->setValues($crawler->getData());
        $this->setForm($form);
        return $this;
    }

    /**
     * Retrieve crawler type
     *
     * @return string
     */
    protected function _getCrawlerType()
    {
        if (Mage::registry('current_crawler')->getId()) {
            return Mage::registry('current_crawler')->getType();
        }

        return $this->getRequest()->getParam('type');
    }
}