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
 * Settings tab block
 * @class Maverick_Crawler_Block_Adminhtml_Crawler_Edit_Tab_Settings
 */

class Maverick_Crawler_Block_Adminhtml_Crawler_Edit_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('maverick_crawler')->__('Continue'),
                    'onclick'   => "setSettings('".$this->getContinueUrl()."','name','type')",
                    'class'     => 'save'
                ))
        );
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('settings', array('legend'=>Mage::helper('maverick_crawler')->__('Create Crawler Settings')));

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => Mage::helper('maverick_crawler')->__('Sort Order'),
            'title'     => Mage::helper('maverick_crawler')->__('Sort Order'),
            'required'  => false
        ));

        $fieldset->addField('type', 'select', array(
            'label'     => Mage::helper('maverick_crawler')->__('Product Type'),
            'title'     => Mage::helper('maverick_crawler')->__('Product Type'),
            'name'      => 'type',
            'value'     => '',
            'required'  => true,
            'values'    => Mage::getSingleton('maverick_crawler/source_crawler_type')->optionsForForm()
        ));

        $fieldset->addField('continue_button', 'note', array(
            'text' => $this->getChildHtml('continue_button'),
        ));

        $this->setForm($form);
    }

    public function getContinueUrl()
    {
        return $this->getUrl('*/*/new', array(
            '_current'  => true,
            'set'       => '{{name}}',
            'type'      => '{{type}}'
        ));
    }
}