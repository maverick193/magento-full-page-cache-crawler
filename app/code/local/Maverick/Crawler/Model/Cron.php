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
 * Cron Model
 */

class Maverick_Crawler_Model_Cron
{
    const CRAWLER_SCHEDULED         = 1;
    const CRAWLER_NOT_SCHEDULED     = 0;

    const XML_PATH_CRON_ENABLED = 'crawler/cron/enabled';

    public function crawl()
    {
        $helper = Mage::helper('maverick_crawler');
        if (!Mage::getStoreConfigFlag(self::XML_PATH_CRON_ENABLED)) {
            return $helper->__('Crawling via Cron disabled in module configuration');
        }

        $errors = array();

        $helper->log(
            $helper->__('###### Start Crawling via Cron Job At %s ######', Mage::getModel('core/date')->date('Y-m-d H:i:s'))
        );

        $crawlers = Mage::getResourceModel('maverick_crawler/crawler_collection')
                            ->addFieldToFilter('scheduled', array('eq' => self::CRAWLER_SCHEDULED))
                            ->filterByStatus(Maverick_Crawler_Model_Crawler::STATUS_ENABLED);

        $helper->log($helper->__('Found %d Crawlers Scheduled/Enabled', $crawlers->count()));

        foreach ($crawlers as $crawler) {
            try {
                $errors = $crawler->run(Maverick_Crawler_Model_Crawler::MODE_CRON);
                if (!empty($errors)) {
                    break;
                }
            } catch (Exception $e) {
                $helper->log($e->getMessage(), Zend_Log::ERR);
                continue;
            }
        }

        $helper->log($helper->__('###### End Crawling via Cron Job ######'));
        $message = empty($errors) ?
                   $helper->__('%d Crawlers Scheduled/Enabled successfully executed', $crawlers->count()) :
                   $helper->__('%d Crawlers did not finished, check your log files if enabled', $crawlers->count());

        return $message;
    }
}