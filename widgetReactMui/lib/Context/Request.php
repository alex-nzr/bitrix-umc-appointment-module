<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2024
 * ==================================================
 * uclinic.kursk - Request.php
 * 03.10.2024 14:35
 * ==================================================
 */
namespace Firstbit\UclinicKursk\Context;

/**
 * @class Request
 * @package Firstbit\UclinicKursk\Context
 */
class Request
{
    /**
     * @return array
     */
    public static function getPostJson(): array
    {
        if(!empty($_POST))
        {
            return $_POST;
        }

        $post = json_decode(file_get_contents('php://input'), true);
        if(is_array($post) && (json_last_error() === JSON_ERROR_NONE))
        {
            return $post;
        }

        return [];//TODO throw Exception with error description
    }

    /**
     * @return string
     */
    public static function getAction(): string
    {
        return (string)$_REQUEST['action'];
    }
}