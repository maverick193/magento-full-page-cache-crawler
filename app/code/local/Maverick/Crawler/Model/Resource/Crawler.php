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
 * Cache crawler resource model
 * @class Maverick_Crawler_Model_Resource_Crawler
 */

class Maverick_Crawler_Model_Resource_Crawler extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Crawler to category linkage table
     *
     * @var string
     */
    protected $_crawlerCategoryTable;

    /**
     * Crawler to cms page linkage table
     *
     * @var string
     */
    protected $_crawlerCmsTable;

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('maverick_crawler/crawler', 'entity_id');
        $this->_crawlerCategoryTable    = $this->getTable('maverick_crawler/type_category');
        $this->_crawlerCmsTable         = $this->getTable('maverick_crawler/type_cms');
    }

    /**
     * Set created_at and updated_at before saving entity
     *
     * @param Mage_Core_Model_Abstract $crawler
     * @return Maverick_Crawler_Model_Resource_Crawler
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $crawler)
    {
        $date = Mage::getSingleton('core/date')->date('Y-m-d H:i:s');
        if (!$crawler->getId()) {
            $crawler->setCreatedAt($date);
        }
        $crawler->setUpdatedAt($date);

        return $this;
    }

    /**
     * Retrieve crawler category identifiers
     *
     * @param Maverick_Crawler_Model_Crawler $crawler
     * @return array
     */
    public function getCategoryIds(Maverick_Crawler_Model_Crawler $crawler)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($this->_crawlerCategoryTable, 'category_id')
            ->where('crawler_id = ?', (int)$crawler->getId());

        return $adapter->fetchCol($select);
    }

    /**
     * Retrieve crawler page identifiers
     *
     * @param Maverick_Crawler_Model_Crawler $crawler
     * @return array
     */
    public function getPageIds(Maverick_Crawler_Model_Crawler $crawler)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($this->_crawlerCmsTable, 'page_id')
            ->where('crawler_id = ?', (int)$crawler->getId());

        return $adapter->fetchCol($select);
    }

    /**
     * Save crawler category relations
     *
     * @param Maverick_Crawler_Model_Crawler $crawler
     * @return $this
     */
    public function saveCategories(Maverick_Crawler_Model_Crawler $crawler)
    {
        $categoryIds    = $crawler->getCategoryIds();
        $oldCategoryIds = $this->getCategoryIds($crawler);

        $crawler->setIsChangedCategories(false);

        $insert = array_diff($categoryIds, $oldCategoryIds);
        $delete = array_diff($oldCategoryIds, $categoryIds);

        $write = $this->_getWriteAdapter();
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $categoryId) {
                if (empty($categoryId)) {
                    continue;
                }
                $data[] = array(
                    'crawler_id'  => (int)$crawler->getId(),
                    'category_id' => (int)$categoryId
                );
            }
            if ($data) {
                $write->insertMultiple($this->_crawlerCategoryTable, $data);
            }
        }

        if (!empty($delete)) {
            foreach ($delete as $categoryId) {
                $where = array(
                    'crawler_id = ?'  => (int)$crawler->getId(),
                    'category_id = ?' => (int)$categoryId,
                );

                $write->delete($this->_crawlerCategoryTable, $where);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $crawler->setAffectedCategoryIds(array_merge($insert, $delete));
            $crawler->setIsChangedCategories(true);
        }

        return $this;
    }

    /**
     * Save crawler cms page relations
     *
     * @param Maverick_Crawler_Model_Crawler $crawler
     * @return $this
     */
    public function savePages($crawler)
    {
        $pageIds    = $crawler->getPageIds();
        $oldPageIds = $this->getPageIds($crawler);

        $crawler->setIsChangedPages(false);

        $insert = array_diff($pageIds, $oldPageIds);
        $delete = array_diff($oldPageIds, $pageIds);

        $write = $this->_getWriteAdapter();
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $pageId) {
                if (empty($pageId)) {
                    continue;
                }
                $data[] = array(
                    'crawler_id'    => (int)$crawler->getId(),
                    'page_id'       => (int)$pageId
                );
            }
            if ($data) {
                $write->insertMultiple($this->_crawlerCmsTable, $data);
            }
        }

        if (!empty($delete)) {
            foreach ($delete as $pageId) {
                $where = array(
                    'crawler_id = ?'    => (int)$crawler->getId(),
                    'page_id = ?'       => (int)$pageId,
                );

                $write->delete($this->_crawlerCmsTable, $where);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $crawler->setAffectedPageIds(array_merge($insert, $delete));
            $crawler->setIsChangedPages(true);
        }

        return $this;
    }
}