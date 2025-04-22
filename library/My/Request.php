<?php
/**
*
*/
class My_Request
{
    public static function isXmlHttpRequest()
    {
        if ((isset($_SERVER['X_REQUESTED_WITH']) && $_SERVER['X_REQUESTED_WITH'] == 'XMLHttpRequest')
            || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
        )
            return true;

        return false;
    }
}