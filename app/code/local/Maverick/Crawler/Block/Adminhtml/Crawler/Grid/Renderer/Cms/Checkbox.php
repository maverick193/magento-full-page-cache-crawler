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
 * Grid checkbox column renderer
 * @class Maverick_Crawler_Block_Adminhtml_Crawler_Grid_Renderer_Checkbox
 */

class Maverick_Crawler_Block_Adminhtml_Crawler_Grid_Renderer_Cms_Checkbox
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Checkbox
{
    /**
     * @param string $value   Value of the element
     * @param bool   $checked Whether it is checked
     * @return string
     */
    protected function _getCheckboxHtml($value, $checked)
    {
        $html = '<input type="checkbox" ';
        $html .= 'name="page_ids[]"';
        $html .= 'value="' . $this->escapeHtml($value) . '" ';
        $html .= 'class="'. ($this->getColumn()->getInlineCss() ? $this->getColumn()->getInlineCss() : 'checkbox') .'"';
        $html .= $checked . $this->getDisabled() . '/>';
        return $html;
    }
}