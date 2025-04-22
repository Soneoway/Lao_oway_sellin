<?php 
/**
* @author buu.pham
*/
class My_Device_UserAgent
{
    public static function isCocCoc()
    {
        $userAgent = new Zend_Http_UserAgent();
        $device    = $userAgent->getDevice();
        $userAgentString = $device->getUserAgent();

        if (strpos($userAgentString, 'coc_coc_browser')) return true;
        return false;
    }
}
