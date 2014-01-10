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
}