<?php

namespace App\Services\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface LanguageServiceInterface
{
    public function paginate($request);
    public function create($request);
    public function update($id, $request);
    public function destroy($id);
    public function switch($id);
    public function saveTranslate($option, $request);
}
