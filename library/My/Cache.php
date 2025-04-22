<?php 
/**
* 
*/
class My_Cache
{
    /**
     * [header description]
     * @param  [type]  $response     [description]
     * @param  boolean $time         [description]
     * @param  integer $expire_time  [description]
     * @param  string  $content_type [description]
     * @return [type]                [description]
     */
    public static function header($response, $time = false, $expire_time = 3600, $content_type = 'text/html')
    {
        if (!$time) $time = time();

        $modifiedTime = date('D, d M Y H:i:s e', $time);

        return $response
            ->setHeader('Last-Modified', $modifiedTime, true)
            ->setHeader('ETag', md5($modifiedTime), true)
            ->setHeader('Expires', date('D, d M Y H:i:s e', $time + $expire_time), true)
            ->setHeader('Pragma', '', true)
            ->setHeader('Cache-Control', 'public,max-age='.$expire_time.',must-revalidate', true)
            ->setHeader('Content-Type', $content_type, true);
    }
}