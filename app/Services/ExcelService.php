<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ExcelService
{
    const XLSX = "xlsx";
    const XLS  = "xls";
    public function processSpreadsheet($filePath): array
    {
        $reader = null;

        if (pathinfo($filePath, PATHINFO_EXTENSION) === self::XLSX) {
            $reader = new Xlsx();
        } elseif (pathinfo($filePath, PATHINFO_EXTENSION) === self::XLS) {
            $reader = new Xls();
        } else {
            $reader = new Csv();
        }

        $spreadsheet = $reader->load($filePath);

        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();
        $data = array_slice($data, 1);

        $dados = [];

        foreach ($data as $row) {
            if (!empty(array_filter($row))) {
                $dados[] = $row;
            }
        }

        return $dados;
    }
}
