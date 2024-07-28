<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class DirectoryHelper
{
    public static function createDirectoryIfNotExists($class, $function)
    {
        $directoryPath = storage_path('app/public/import');
        $destinationDirectory = storage_path('app/public/processed');

        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath);
            chmod($directoryPath, 0777);

            Log::info("Criado diretorio de arquivos {$directoryPath} com sucesso, " . " Class: " . $class . " Function: " . $function . " Line: ". __line__);
        }

        if (!File::exists($destinationDirectory)) {
            File::makeDirectory($destinationDirectory);
            chmod($destinationDirectory, 0777);
            Log::info("Criado diretorio de arquivos processados {$destinationDirectory} com sucesso, " . " Class: " . $class . " Function: " . $function . " Line: ". __line__);
        }
    }
}