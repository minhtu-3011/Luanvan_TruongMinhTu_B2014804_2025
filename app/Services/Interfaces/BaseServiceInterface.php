<?php

namespace App\Services\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface BaseServiceInterface
{
    public function currentLanguage();
    public function formatAlbum($request);
    public function nestedset();
}
