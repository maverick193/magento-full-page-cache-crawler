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
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('maverick_crawler/crawler', 'entity_id');
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
}