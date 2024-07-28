<?php

namespace App\Http\Controllers;

use App\Services\ExcelService;
use App\Utilities\DirectoryHelper;
use App\Http\Requests\StoreUploadRequest;

class UploadController extends Controller
{
    public $arrayData;
    public function __construct(private ExcelService $excelService)
    {
        $this->arrayData = [];
    }
    public function index()
    {
        return view('index');
    }
    public function store(StoreUploadRequest $request)
    {
        DirectoryHelper::createDirectoryIfNotExists(__CLASS__, __FUNCTION__);

        $file = $request->file('excel_file');
        $fileName = $file->getClientOriginalName();
        $file->storeAs('import', $fileName, 'public');
        $directoryPath = storage_path('app/public/import') . "/{$fileName}";

        $this->arrayData = $this->excelService->processSpreadsheet($directoryPath);

        if (empty($this->arrayData)) {
            return back()->withErrors(['message' => 'Erro no processamento do arquivo!'])->withInput();
        }

        return redirect()->back()->with('success', 'Arquivo de cobrança enviado com sucesso!');
    }
}
