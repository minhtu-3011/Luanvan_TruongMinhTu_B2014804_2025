<?php

namespace App\Services\Interfaces;

/**
 * Interface {$module}ServiceInterface
 * @package App\Services\Interfaces
 */
interface MenuServiceInterface
{
    public function paginate($request, $languageId);
}
