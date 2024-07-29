<?php

namespace App\Http\Controllers;

use App\Utilities\FileHelper;
use App\Services\ExcelService;
use App\Utilities\DirectoryHelper;
use App\Http\Requests\StoreUploadRequest;
use App\Repositories\EloquentInvoiceRepository;



class UploadController extends Controller
{
    public $arrayData;
    public function __construct(private ExcelService $excelService, private EloquentInvoiceRepository $invoiceRepository)
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
        $filePath = $file->storeAs('import', $fileName, 'public');
        $directoryPath = storage_path('app/public/import') . "/{$fileName}";

        $this->arrayData = $this->excelService->processSpreadsheet($directoryPath);

        if (empty($this->arrayData)) {
            FileHelper::moveAndDeleteFile($filePath, $fileName, __CLASS__, __FUNCTION__, true);
            return back()->withErrors(['message' => 'Erro no processamento do arquivo!'])->withInput();
        }

        $this->invoiceRepository->add($this->arrayData);

        FileHelper::moveAndDeleteFile($filePath, $fileName, __CLASS__, __FUNCTION__);

        return redirect()->back()->with('success', 'Arquivo de cobrança enviado com sucesso!');
    }
}
