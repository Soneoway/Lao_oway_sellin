<?php
/**
* 
*/
class My_Image_Barcode
{    
    public static function render($code = '', $uploaded_dir = null, $type = 'code39')
    {
        if (!$uploaded_dir)
            $uploaded_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' 
                . DIRECTORY_SEPARATOR .'public' . DIRECTORY_SEPARATOR . 'photo' 
                . DIRECTORY_SEPARATOR . 'barcode';

        $barcode_file = $uploaded_dir.DIRECTORY_SEPARATOR.$code.'.jpg';

        if (!file_exists($barcode_file)) {

            $barcodeOptions = array(
                'text'        => $code,
                'barHeight'   => 50,
                'factor'      => 1,
                'font'        => 5,
                'stretchText' => true,
            );
 
            // No required options
            $rendererOptions = array();
             
            // Draw the barcode in a new image,
            $imageResource = Zend_Barcode::factory(
                $type, 'image', $barcodeOptions, $rendererOptions
            );

            if (!is_dir($uploaded_dir))
                @mkdir($uploaded_dir, 0777, true);

            $rc = $imageResource->draw();

            imagejpeg($rc, $barcode_file, 100);
            imagedestroy($rc);
        }
    }
}

        

        