<?php

include_once ROOT_DIR . '/files.php';

/**
 * Plugin that creates a route to image thumbnails
 * with a caching mechanism
 *
 * @author Alexandre Kaspar
 */
class Image_Thumbnails {

    /**
     * Orientation mode
     *
     * @value 0 no auto-orientation
     * @value 1 only auto-orient the thumbnail
     * @value 2 auto-orient the original image (and thumbnail)
     */
    private $orient;

    public function __construct() {
        $this->orient = 0;
    }

    public function config_loaded($config){
        if(array_key_exists('thumbnail_orient', $config)){
            $this->orient = $config['thumbnail_orient'];
        }
    }

    /**
     * Auto-rotate an image
     *
     * @param Imagick $im the imagick image
     * @return whether the image changed (and thus deserves being saved)
     */
    public static function auto_rotate($im){
        switch($im->getImageOrientation()){
            case imagick::ORIENTATION_BOTTOMRIGHT:
                $im->rotateImage("#000", 180);
                break;
            case imagick::ORIENTATION_RIGHTTOP:
                $im->rotateImage("#000", 90);
                break;
            case imagick::ORIENTATION_LEFTBOTTOM:
                $im->rotateImage("#000", -90);
                break;
            default:
                return FALSE;
        }
        $im->setImageOrientation(imagick::ORIENTATION_TOPLEFT);
        return TRUE;
    }

    public function request_url(&$route){
        // is a thumbnail requested?
        $atpos = strrpos($route, '@');
        if(!$atpos)
            return; // definitely not a thumbnail
        $img_route = substr($route, 0, $atpos);
        $thumb_size = substr($route, $atpos + 1);
        if(!$thumb_size || !preg_match('/^[0-9]{1,3}x[0-9]{1,3}$/', $thumb_size))
            return;

        $base_dir = Files::base_dir();
        $img_file = $base_dir . $img_route;

        // only process valid images
        if(!@getimagesize($img_file))
            return;

        // get cache location
        global $config;
        if(array_key_exists('thumbnail_dir', $config)){
            $thumb_dir = $config[$thumbnail_dir];
        } else {
            $thumb_dir = '.thumbnails';
        }
        $dir = $base_dir . '/' . $thumb_dir;
        if(!is_dir($dir) && !mkdir($dir)){
            return; // we could not do anything
        }

        // get thumbnail cache path
        $img_route = Text::clean_slashes($img_route);
        $thumb_route = '/' . $thumb_dir . '/' . $thumb_size . str_replace('/', '@', $img_route);
        $thumb_file = Text::clean_slashes($base_dir . $thumb_route);

        if(!is_file($thumb_file)){
            // get thumbnail size
            list($thumb_width, $thumb_height) = explode('x', $thumb_size);

            // crop image
            $im = new Imagick($img_file);

            // auto-orientation
            if($this->orient > 0 && self::auto_rotate($im)){
                // should we store the image?
                if($this->orient >= 2){
                    try{
                        $im->writeImage();
                    }catch(ImagickException $ex){}
                }
            }

            $im->cropThumbnailImage( $thumb_width, $thumb_height );
            $im->writeImage($thumb_file);
        }

        $base_url = Request::base_url();
        header('Status: 301 Moved Permanently', false, 301);
        header("Location: $base_url$thumb_route");
        exit;
    }

}

?>
