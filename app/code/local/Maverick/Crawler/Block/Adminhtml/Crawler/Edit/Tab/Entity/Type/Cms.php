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
 * CMS tab block
 * @class Maverick_Crawler_Block_Adminhtml_Crawler_Edit_Tab_Entity_Type_Cms
 */

class Maverick_Crawler_Block_Adminhtml_Crawler_Edit_Tab_Entity_Type_Cms extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('crawler_cms_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('cms/page')->getCollection();
        /* @var $collection Mage_Cms_Model_Mysql4_Page_Collection */
        $collection->setFirstStoreFlag(true);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('in_pages', array(
            'header_css_class' => 'a-center',
            'name'      => 'in_pages',
            'values'    => $this->_getSelectedPages(),
            'align'     => 'center',
            'renderer'  => 'maverick_crawler/adminhtml_crawler_grid_renderer_cms_checkbox',
            //'type'      => 'checkbox',
            'index'     => 'page_id'
        ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('maverick_crawler')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));

        $this->addColumn('identifier', array(
            'header'    => Mage::helper('maverick_crawler')->__('URL Key'),
            'align'     => 'left',
            'index'     => 'identifier'
        ));

        $this->addColumn('root_template', array(
            'header'    => Mage::helper('maverick_crawler')->__('Layout'),
            'index'     => 'root_template',
            'type'      => 'options',
            'options'   => Mage::getSingleton('page/source_layout')->getOptions(),
        ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('maverick_crawler')->__('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => Mage::getSingleton('cms/page')->getAvailableStatuses()
        ));

        $this->addColumn('creation_time', array(
            'header'    => Mage::helper('maverick_crawler')->__('Date Created'),
            'index'     => 'creation_time',
            'type'      => 'datetime',
        ));

        $this->addColumn('page_actions', array(
            'header'    => Mage::helper('maverick_crawler')->__('Action'),
            'width'     => 10,
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'adminhtml/cms_page_grid_renderer_action',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        $type   = $this->_getCrawlerType();
        $action = Mage::getSingleton('maverick_crawler/source_crawler_type')->getOptionAction($type);
        return $this->getUrl("*/*/{$action}", array('_current'=>true));
    }

    /**
     * Retrieve crawler type
     *
     * @return string
     */
    protected function _getCrawlerType()
    {
        $crawler = $this->_getCrawler();
        if (($crawler instanceof Maverick_Crawler_Model_Crawler) && ($crawler->getId())) {
            return $crawler->getType();
        }

        return $this->getRequest()->getParam('type');
    }

    /**
     * Retrieve currently edited crawler model
     *
     * @return Maverick_Crawler_Model_Crawler
     */
    protected function _getCrawler()
    {
        return Mage::registry('current_crawler');
    }

    /**
     * Retrieve selected cms pages
     *
     * @return array
     */
    protected function _getSelectedPages()
    {
        $pages = $this->_getCrawler()->getPageIds();
        return $pages;
    }
}