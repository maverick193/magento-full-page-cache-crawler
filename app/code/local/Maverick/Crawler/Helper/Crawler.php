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
 * Crawler helper
 * @class Maverick_Crawler_Helper_Crawler
 */
require_once Mage::getBaseDir() . '/vendor/autoload.php';

class Maverick_Crawler_Helper_Crawler extends Mage_Core_Helper_Abstract
{
    protected $_client;

    /**
     * Init client
     */
    public function __construct()
    {
        $this->_client = new Goutte\Client;
    }

    public function visit($url, $repeat = 1)
    {
        if (empty($url)) {
            //@todo log
            return;
        }

        for ($i=0; $i<$repeat; $i++) {
            $crawler = $this->_client->request('GET', $url);
        }

        $links = $crawler->filter('a');
        foreach ($links as $index => $link) {
            $href = $link->getAttribute('href');
            $this->_client->request('GET', $href);
        }
    }
}