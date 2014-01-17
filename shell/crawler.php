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
 * Maverick shell crawler
 * @class Maverick_Shell_Crawler
 */
 
require_once 'abstract.php';

class Maverick_Shell_Crawler extends Mage_Shell_Abstract
{

    /**
     * Run script
     *
     */
    public function run()
    {
        if ($this->getArg('crawl') && $this->getArg('id')) {
            try {
                $id         = $this->getArg('id');
                $crawler    = Mage::getModel('maverick_crawler/crawler')->load($id);
                $scan       = $this->getArg('scan') ? $this->getArg('scan') : false;

                if (!$crawler->getId()) {
                    Mage::throwException(
                        Mage::helper('maverick_crawler')->__('Unable To Find Crawler (ID : %s)', $id)
                    );
                }

                $helper = Mage::helper('maverick_crawler');
                echo $helper->__('### Starting Crawler "%s" (ID %s)', $crawler->getName(), $id) . "\n";

                $factory    = $crawler->getFactory();
                $urls       = $factory->getUrls();
                $countUrls  = count($urls);

                $start = $time = time();

                foreach ($urls as $url) {
                    $url = Mage::getBaseUrl('web') . 'index.php' . $url . '.html';
                    $crawlerObj = $this->_runUrl($factory, $url);
                    if (is_string($crawlerObj)) {
                        echo '    ->' . $crawlerObj . "\n";
                        continue;
                    }
                    if ($scan) {
                        $scanedUrls = $factory->scanUrl($crawlerObj);
                        $countUrls += count($scanedUrls);

                        echo $helper->__('    -->Scanning Links for "%s" found %d urls', $url, count($scanedUrls)) . "\n" . "\n";
                        echo $helper->__('Crawling Them ...') . "\n";
                        foreach ($scanedUrls as $scanedUrl) {
                            $res = $this->_runUrl($factory, $scanedUrl);
                            if (is_string($res)) {
                                echo '    ->' . $res . "\n";
                            }
                        }
                    }
                }

                $totalTime = date("H:i:s", (int)(time() - $start));
                echo "\n" . "\n";
                echo $helper->__('TOTAL TIME %s - %s urls crawled', $totalTime, $countUrls). "\n";
            } catch (Mage_Core_Exception $e) {
                echo $e->getMessage() . "\n";
            } catch (Exception $e) {
                echo Mage::helper('maverick_crawler')->__('Crawler unknown error:\n');
                echo $e . "\n";
            }
        } else {
            echo $this->usageHelp();
        }
    }

    protected function _runUrl($factory, $url)
    {
        echo Mage::helper('maverick_crawler')->__('->Warming Up %s', $url) . "\n";
        $time       = time();
        $crawlerObj = $factory->runUrl($url);
        echo Mage::helper('maverick_crawler')->__('    -->Took %s seconds', (int)(time() - $time)) . "\n" . "\n";
        return $crawlerObj;
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage   : php crawler.php crawl --id [ID] --scan [VALUE]
---------
    [ID]    -> Id of crawler saved in backend
    [VALUE] -> 1 or 0, scan the url to get all the links of the page and generate their cache

Exemple : php crawler.php crawl --id 19 --scan 1
---------

Help    : php crawler.php --help
---------\n
USAGE;
    }
}

$shell = new Maverick_Shell_Crawler();
$shell->run();
