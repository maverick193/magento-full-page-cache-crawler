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
 * Adminhtml crawler grid block
 * @class Maverick_Crawler_Block_Adminhtml_Crawler_Grid
 */

class Maverick_Crawler_Block_Adminhtml_Crawler_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Grid initialization
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('maverick_crawler_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare Grid Columns
     * @see Mage_Adminhtml_Block_Widget_Grid::_prepareColumns
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('maverick_crawler')->__('ID'),
            'width'     => '80px',
            'type'      => 'text',
            'align'     => 'center',
            'index'     => 'entity_id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('maverick_crawler')->__('Name'),
            'index'     => 'name',
            'type'      => 'text',
            'align'     => 'center',
        ));

        $this->addColumn('type', array(
            'header'    => Mage::helper('maverick_crawler')->__('Crawler Type'),
            'index'     => 'type',
            'type'      => 'options',
            'options'   => Mage::getSingleton('maverick_crawler/source_crawler_type')->optionForGrid(),
            'align'     => 'center',
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('maverick_crawler')->__('Status'),
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                Maverick_Crawler_Model_Crawler::STATUS_DISABLED => Mage::helper('maverick_crawler')->__('Disabled'),
                Maverick_Crawler_Model_Crawler::STATUS_ENABLED  => Mage::helper('maverick_crawler')->__('Enabled')
            ),
        ));

        $this->addColumn('scheduled', array(
            'header'    => Mage::helper('maverick_crawler')->__('Scheduled'),
            'index'     => 'scheduled',
            'type'      => 'options',
            'options'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
        ));

        $this->addColumn('scan', array(
            'header'    => Mage::helper('maverick_crawler')->__('Scan Option'),
            'index'     => 'scan',
            'type'      => 'options',
            'options'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
        ));

        $this->addColumn('last_execution_at', array(
            'header'    => Mage::helper('maverick_crawler')->__('Last Execution at'),
            'index'     => 'last_execution_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('last_execution_mode', array(
            'header'    => Mage::helper('maverick_crawler')->__('Last Execution Mode'),
            'index'     => 'last_execution_mode',
            'type'      => 'text',
            'align'     => 'center',
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('maverick_crawler')->__('Created at'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('maverick_crawler')->__('Updated at'),
            'index'     => 'updated_at',
            'type'      => 'datetime',
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('maverick_crawler')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('maverick_crawler')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Prepare crawler collection
     *
     * @access protected
     * @see Mage_Adminhtml_Block_Widget_Grid::_prepareCollection()
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('maverick_crawler/crawler_collection');

        $this->setCollection($collection);
        parent::_prepareCollection();
    }

    /**
     * Return row url for js event handlers
     *
     * @param Maverick_Crawler_Model_Crawler|Varien_Object
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }

    /**
     * Return Grid URL for AJAX query
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * Prepare Massaction Toolbar
     *
     * @return Maverick_Crawler_Block_Adminhtml_Crawler_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('crawler');

        $this->getMassactionBlock()->addItem('delete',
            array(
                'label'     => Mage::helper('maverick_crawler')->__('Delete Crawler'),
                'url'       => $this->getUrl('*/*/massDelete'),
                'confirm'   => Mage::helper('maverick_crawler')->__('Are you sure?')
            ));

        return $this;
    }
}