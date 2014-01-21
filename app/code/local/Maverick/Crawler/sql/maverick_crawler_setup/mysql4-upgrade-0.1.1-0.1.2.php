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
 * Upgrade script
 *
 * The file was named mysql4-upgrade-0.1.1-0.1.2.php for
 * compatibility with versions < 1.6
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'maverick_crawler/type_cms'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('maverick_crawler/type_cms'))
    ->addColumn('crawler_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ), 'Crawler ID')
    ->addColumn('page_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ), 'Page ID')
    ->addIndex($installer->getIdxName('maverick_crawler/type_cms', array('page_id')),
        array('page_id'))
    ->addForeignKey($installer->getFkName('maverick_crawler/type_cms', 'crawler_id', 'maverick_crawler/crawler', 'entity_id'),
        'crawler_id', $installer->getTable('maverick_crawler/crawler'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('maverick_crawler/type_cms', 'page_id', 'cms/page', 'page_id'),
        'page_id', $installer->getTable('cms/page'), 'page_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Crawler To CMS Page Linkage Table');

$installer->getConnection()->createTable($table);

$installer->endSetup();