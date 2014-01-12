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
 * Cache crawler type source model
 * @class Maverick_Crawler_Model_Source_Crawler_Type
 */

class Maverick_Crawler_Model_Source_Crawler_Type
{
    protected $_options;

    /**
     * Get options in "key-value" format for grid
     *
     * @return array
     */
    public function optionForGrid()
    {
        if (!$this->_options) {
            $options = array();

            $entities = Mage::app()->getConfig()->getNode('crawler/entities');

            foreach ($entities->children() as $entity) {
                if (!$entity->label || !$entity->class) {
                    continue;
                }

                $options[(string)$entity->class] = Mage::helper('maverick_crawler')->__((string)$entity->label);
            }

            $this->_options = $options;
        }

        return $this->_options;
    }

    /**
     * Get options in "value", "label" format for form
     *
     * @return array
     */
    public function optionsForForm()
    {
        $options = $this->optionForGrid();
        $result = array();

        foreach ($options as $value => $label) {
            $result[] = array('label' => $label, 'value' => $value);
        }

        array_unshift($result, array(
            'label' => Mage::helper('maverick_crawler')->__('-- Please Choose a Crawler Type --'),
            'value' => ''
        ));

        return $result;
    }

    public function getOptionAction($option)
    {
        $entities = Mage::app()->getConfig()->getNode('crawler/entities');
        foreach ($entities->children() as $entity) {
            if (!$entity->label || !$entity->class || !$entity->action) {
                continue;
            }

            if ((string)$entity->class == $option) {
                return $entity->action;
            }
        }
        return false;
    }
}