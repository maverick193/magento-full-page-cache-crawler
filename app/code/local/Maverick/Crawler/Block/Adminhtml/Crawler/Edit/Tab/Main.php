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
        $fieldset = $form->addFieldset('main', array('legend'=>Mage::helper('maverick_crawler')->__('General Information')));

        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('maverick_crawler')->__('Crawler Name'),
            'title'     => Mage::helper('maverick_crawler')->__('Crawler Name'),
            'name'      => 'name',
            'required'  => false
        ));

        $this->setForm($form);
    }
}