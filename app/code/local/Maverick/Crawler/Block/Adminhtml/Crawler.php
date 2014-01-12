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
 * Adminhtml crawler entity list block
 * @class Maverick_Crawler_Block_Adminhtml_Crawler
 */

class Maverick_Crawler_Block_Adminhtml_Crawler extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        /**
         * In parent::_prepareLayout() :
         * $this->getLayout()->createBlock($this->_blockGroup.'/' . $this->_controller . '_grid')
         */
        $this->_blockGroup      = 'maverick_crawler';
        $this->_controller      = 'adminhtml_crawler';
        $this->_headerText      = Mage::helper('maverick_crawler')->__('Cache Warmers');
        $this->_addButtonLabel  = Mage::helper('maverick_crawler')->__('Add New Crawler');
        parent::__construct();
    }
}