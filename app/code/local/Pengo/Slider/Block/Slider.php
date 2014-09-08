<?php

/**
 * @author Javier Telio
 * @package Pengo_Slider
 * @link  http://nivo.dev7studios.com/
 */
class Pengo_Slider_Block_Slider extends Mage_Core_Block_Template {

    /**
     * set template 
     * @var type 
     */
    protected $_template = 'slider/slider.phtml';

    /**
     * map of all options 
     * @var type 
     */
    protected $_mapOptions = array(
        'effect' => 'effect',
        'slices' => 'slices',
        'boxCols' => 'box_cols',
        'boxRows' => 'box_rows',
        'animSpeed' => 'animation_speed',
        'pauseTime' => 'pause_time',
        'startSlide' => 'start_slide',
        'directionNav' => 'direction_nav',
        'controlNav' => 'control_nav',
        'controlNavThumbs' => 'control_nav_thumbs',
        'pauseOnHover' => 'pause_on_hover',
        'manualAdvance' => 'manual_advance',
        'prevText' => 'prev_text',
        'nextText' => 'next_text',
        'randomStart' => 'random_start',
        'directionNavHide' => 'direction_nav_hide',
    );

    /**
     * identifies the numeric fields
     * @var type 
     */
    protected $_numerics = array(
        'slices',
        'boxCols',
        'boxRows',
        'animationSpeed',
        'pauseTime',
        'startSlide',
    );

    protected function _construct() {
        parent::_construct();
        $this->setTemplate($this->_template);
    }

    /**
     * create slider
     * @param type $id
     * @return type 
     */
    public function createSlider($id = 2) {

        $html = '<div class="theme-default">
                 <div id="slider" class="nivoSlider">';

        $html .= $this->createBodySlider($id);
        $html .= '</div>
                 </div>';
        $html .= '<script type="text/javascript"> 
                // <![CDATA[
                jQuery.noConflict();
                jQuery(window).load(function() {
                jQuery("#slider").nivoSlider({';
        $html .= $this->createJs($id);
        $html .='});
            });
            // ]]>
            </script>';
        return $html;
    }

    /**
     * get colection gallery
     * @param type $id
     * @return type 
     */
    public function getGallery($idSlider) {

        return Mage::getModel('slider/gallery')
                        ->getCollection()
                        ->addFieldToFilter('slider_id', $idSlider)
                        ->load()
                        ->getItems();
    }

    /**
     * Create body to slider Nivo
     * @param type $idSlider
     * @return string 
     */
    public function createBodySlider($id) {

        $html = '';
        $path = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'slider';

        foreach ($this->getGallery($id) as $image) {
            $promotionId = ($image->getId()) ? $image->getId() : '';
            $link = ($image->getImgLink()) ? $image->getImgLink() : '';
            $title = ($image->getImgLabel()) ? $image->getImgLabel() : '';
            $target = ($image->getImgTarget()) ? ' onclick="this.target=\'' . $image->getImgTarget() . '\'"' : '';

            if (!empty($link)) {
                $html .= '<a href="' . $link . '"' . $target . ' >';
            }

            $html .='<img class="gtm-promotion" src="' . $path . $image->getImgName() . '" promotion="'.$promotionId.'" alt="" title="' . $title . ' "/>';

            if (!empty($link)) {
                $html .= '</a>';
            }
        }
        return $html;
    }

    /**
     * Create script with all option to slider Nivo
     * @param type $sliderOption
     * @return string 
     */
    public function createJs($sliderOption) {
        $js = '';
        $options = $this->getAllOptions($sliderOption)->getData();
        $map = array_flip($this->_mapOptions);

        foreach ($options as $option => $value) {
            if (array_key_exists($option, $map)) {
                $option = lcfirst($this->_camelize($option));

                if (is_null($value) || $value == '') {
                    continue;
                } elseif (is_numeric($value) && !in_array($option, $this->_numerics)) {
                    $value = $value ? 'true' : 'false';
                } elseif (is_numeric($value)) {
                    
                } elseif (is_string($value)) {
                    $value = '\'' . $value . '\'';
                }

                $js .= $option . ':' . $value . ',';
            }
        }
        return $js;
    }

    /**
     * return array with all option for slider script
     * @param type $id
     * @return type 
     */
    private function getAllOptions($id) {
        return Mage::getModel('slider/slider')
                        ->load($id);
    }

}