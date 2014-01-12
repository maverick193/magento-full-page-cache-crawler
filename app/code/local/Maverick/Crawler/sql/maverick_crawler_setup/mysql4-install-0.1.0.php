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
 * Install script
 *
 * The file was named mysql4-install-0.1.0.php (not install-0.1.0.php) for
 * compatibility with versions < 1.6
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * drop table 'maverick_crawler/crawler' if it exists
 */
if ($installer->getConnection()->isTableExists($installer->getTable('maverick_crawler/crawler'))) {
    $installer->getConnection()->dropTable($installer->getTable('maverick_crawler/crawler'));
}

/**
 * Create table 'maverick_crawler/crawler'
 */

$table = $installer->getConnection()
    ->newTable($installer->getTable('maverick_crawler/crawler'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Entity Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
    ), 'Entity Name')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
    ), 'Entity Type')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Entity Status')
    ->addColumn('scheduled', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Crawler Scheduled In a Cron Job')
    ->addColumn('last_execution_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    ), 'Last Execution Date Time')
    ->addColumn('last_execution_mode', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Last Execution Mode')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'        => false,
    ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'        => false,
    ), 'Updated At')
    ->setComment('Cache Crawler Entities');

$installer->getConnection()->createTable($table);

/**
 * Create table 'maverick_crawler/type_category'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('maverick_crawler/type_category'))
    ->addColumn('crawler_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ), 'Crawler ID')
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ), 'Category ID')
    ->addIndex($installer->getIdxName('maverick_crawler/type_category', array('category_id')),
        array('category_id'))
    ->addForeignKey($installer->getFkName('maverick_crawler/type_category', 'crawler_id', 'maverick_crawler/crawler', 'entity_id'),
        'crawler_id', $installer->getTable('maverick_crawler/crawler'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('maverick_crawler/type_category', 'category_id', 'catalog/category', 'entity_id'),
        'category_id', $installer->getTable('catalog/category'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Crawler To Category Linkage Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();