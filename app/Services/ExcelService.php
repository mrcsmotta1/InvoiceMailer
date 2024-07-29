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

        $header = [];
        $line = $worksheet->getRowIterator(1)->current();
        foreach ($line->getCellIterator() as $celula) {
            $header[] = $this->removeAccentAndNormalize($celula->getValue());
        }

        foreach ($data as $row) {
            if (!empty($row)) {
                $row[4] = floatval(str_replace(['R$', ',', '.'], '', $row['4']));
                $dados[] = array_combine($header, $row);
            }
        }

        return $dados;
    }

    public function removeAccentAndNormalize($data)
    {
            $withAccent = preg_replace('/[`^~\'"]/', null, iconv('UTF-8', 'ASCII//TRANSLIT', $data));
            $normalizedString = str_replace(' ', '_', str_replace('-', '', trim($withAccent)));
            $normalized = strtolower($normalizedString);

        return $normalized;
    }
}
