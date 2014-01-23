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
 * Cache crawler adminhtml controller
 * @class Maverick_Crawler_Adminhtml_CrawlerController
 */

class Maverick_Crawler_Adminhtml_CrawlerController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check ACL permissions
     * @todo check ACL permissions
     */
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('maverick/crawler');
    }

    /**
     * Load layout, set breadcrumbs
     *
     * @return Maverick_Crawler_Adminhtml_CrawlerController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('maverick/crawler')
            ->_addBreadcrumb(
                Mage::helper('maverick_crawler')->__('Maverick'),
                Mage::helper('maverick_crawler')->__('Maverick'))
            ->_addBreadcrumb(
                Mage::helper('maverick_crawler')->__('Cache Crawler'),
                Mage::helper('maverick_crawler')->__('Cache Crawler')
            );

        return $this;
    }

    /**
     * Initialize crawler instance
     *
     * @return Maverick_Crawler_Adminhtml_CrawlerController
     */
    protected function _initCrawler()
    {
        $id         = (int) $this->getRequest()->getParam('id');
        $crawler    = Mage::getModel('maverick_crawler/crawler');

        if ($id) {
            $crawler->load($id);
            if (!$crawler->getId()) {
                Mage::throwException($this->__('No crawler with ID "%s" found.', $id));
            }
        }

        Mage::register('current_crawler', $crawler);
        return $this;
    }

    /**
     * Display cache crawler entity list
     */
    public function indexAction()
    {
        $this->_title($this->__('Maverick'))->_title($this->__('Cache Crawler'));
        $this->_initAction()
            ->_addContent(
                $this->getLayout()->createBlock('maverick_crawler/adminhtml_crawler', 'maverick_crawler')
            )
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_initCrawler();
        $id         = (int) $this->getRequest()->getParam('id');
        $crawler    = Mage::registry('current_crawler');

        if ($id && !$crawler->getId()) {
            $this->_getSession()->addError(Mage::helper('maverick_crawler')->__('This crawler no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($this->__('Maverick'));
        if ($crawler->getId()) {
            $this->_title($crawler->getName());
        } else {
            $this->_title($this->__('New Crawler'));
        }

        $this->_initAction()
             ->_addContent($this->getLayout()->createBlock('maverick_crawler/adminhtml_crawler_edit'))
             ->_addLeft($this->getLayout()->createBlock('maverick_crawler/adminhtml_crawler_edit_tabs'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->renderLayout();
    }

    public function validateAction()
    {
        $data = $this->getRequest()->getPost();
        if (empty($data) || !isset($data['type'])) {
            $this->_getSession()->addError(Mage::helper('maverick_crawler')->__('Cannot retrieve Crawler type.'));
            $this->_redirect('*/*/new');
            return;
        }

        $this->_redirect('*/*/edit', array('type' => $data['type']));
        return;
    }

    /**
     * Categories tree tab
     */
    public function categoriesAction()
    {
        $this->_initCrawler();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock(
                'maverick_crawler/adminhtml_crawler_edit_tab_entity_type_category',
                'crawler_categories'
            )->toHtml()
        );
    }

    /**
     * CMS Page tab
     */
    public function cmsAction()
    {
        $this->_initCrawler();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock(
                'maverick_crawler/adminhtml_crawler_edit_tab_entity_type_cms',
                'crawler_cms'
            )->toHtml()
        );
    }

    public function categoriesJsonAction()
    {
        $this->_initCrawler();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('maverick_crawler/adminhtml_crawler_edit_tab_entity_type_category')
                 ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    protected function _initSaveCrawler($data)
    {
        $this->_initCrawler();
        $crawler = Mage::registry('current_crawler');

        if (!isset($data['name']) || empty($data['name'])) {
            $data['name'] = 'crawler_' . Mage::getSingleton('core/date')->date('YmdHis');
        }

        if(!isset($data['hidden_type']) || empty($data['hidden_type'])) {
            $type = Mage::registry('current_crawler')->getType() ?
                    Mage::registry('current_crawler')->getType() :
                    $this->getRequest()->getParam('type');
        } else {
            $type = $data['hidden_type'];
        }
        $data['type'] = $type;

        $crawler->addData($data);

        return $crawler;
    }

    public function saveAction()
    {
        $data       = $this->getRequest()->getPost();
        $session    = Mage::getSingleton('adminhtml/session');

        if ($data) {
            $redirectBack   = $this->getRequest()->getParam('back', false);
            $crawler        = $this->_initSaveCrawler($data);

            try {
                $crawler->save();
                $types  = Mage::getSingleton('maverick_crawler/source_crawler_type')->optionForGrid();
                $label  = isset($types[$crawler->getType()]) ? $types[$crawler->getType()] : '';

                $session->addSuccess($this->__('%s Crawler (ID %s) has been saved.', $label, $crawler->getId()));

                if ($crawler->getScheduled() && !Mage::getStoreConfigFlag('crawler/cron/enabled')) {
                    $session->addNotice(
                        $this->__('Crawler has been set to "Scheduled", don\'t forget to enable cron jobs in <a href="%s" target="_blank">module configuration</a>',
                            $this->getUrl('adminhtml/system_config/edit', array('section' => 'crawler'))
                        )
                    );
                }

                if ($redirectBack) {
                    $this->_redirect('*/*/edit', array('id' => $crawler->getId(),'_current'=>true));
                } else {
                    $this->_redirect('*/*/', array());
                }
                return;
            } catch (Exception $e) {
                $session->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('_current' => true));
                return;
            }
        }

        $session->addError($this->__('Error encountered while retrieving data'));
        $this->_redirect('*/*/edit');
        return;
    }

    /**
     * Delete crawler action
     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $crawler    = Mage::getModel('maverick_crawler/crawler')->load($id);
            $name       = $crawler->getName();

            try {
                $crawler->delete();
                $this->_getSession()->addSuccess($this->__('The crawler "%s" has been deleted.', $name));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }

        $this->_getSession()->addError(Mage::helper('maverick_crawler')->__('Unable to find a crawler to delete.'));
        $this->_redirect('*/*/');
    }

    /**
     * Mass delete crawler action
     */
    public function massDeleteAction()
    {
        $crawlerIds = $this->getRequest()->getParam('crawler');
        if (!is_array($crawlerIds)) {
            $this->_getSession()->addError($this->__('Please select crawler(s).'));
        } else {
            if (!empty($crawlerIds)) {
                try {
                    foreach ($crawlerIds as $crawlerId) {
                        $product = Mage::getSingleton('maverick_crawler/crawler')->load($crawlerId);
                        $product->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($crawlerIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }

    public function runAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $this->_initCrawler();
                $crawler    = Mage::registry('current_crawler');
                $errors     = $crawler->run();

                if (empty($errors)) {
                    $this->_getSession()->addSuccess(
                        $this->__('Crawler has been succefully executed.')
                    );
                } else {
                    foreach ($errors as $error) {
                        $this->_getSession()->addError($error);
                    }
                    $this->_getSession()->addNotice(
                        Mage::helper('maverick_crawler')->__('Crawler did not finish crawling all requested urls')
                    );
                }
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }

            $this->_redirect('*/*/edit', array('id' => $id));
            return;
        }

        $this->_getSession()->addError(Mage::helper('maverick_crawler')->__('Unable to find a crawler to run.'));
        $this->_redirect('*/*/');
    }
}