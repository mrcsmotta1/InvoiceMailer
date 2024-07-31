<?php

namespace App\Http\Controllers;

use App\Utilities\FileHelper;
use App\Services\ExcelService;
use App\Services\SendBarcodeMail;
use App\Utilities\DirectoryHelper;
use Illuminate\Support\MessageBag;
use App\Http\Requests\StoreUploadRequest;
use App\Repositories\EloquentInvoiceRepository;

class UploadController extends Controller
{
    public $arrayData;
    public function __construct(
        private ExcelService $excelService,
        private EloquentInvoiceRepository $invoiceRepository,
        private SendBarcodeMail $sendBarcodeMail
    ) {
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

            $errors = new MessageBag();
            $errors->add('custom_error', 'Erro no processamento do arquivo!');
            return response()->view('index', compact('errors'), 404);
        }

        $this->invoiceRepository->add($this->arrayData);

        $this->sendBarcodeMail->sendEmail($this->arrayData, $request);

        FileHelper::moveAndDeleteFile($filePath, $fileName, __CLASS__, __FUNCTION__);

        // return redirect()->back(201)->with('success', 'Arquivo de cobrança enviado com sucesso!');
        $successMessage = 'Arquivo de cobrança enviado com sucesso!';
        return redirect()->route('upload.excel.index')->with('success', $successMessage)->setStatusCode(201);
    }
}
