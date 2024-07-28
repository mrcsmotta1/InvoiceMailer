<?php

namespace App\Http\Controllers;

use App\Utilities\DirectoryHelper;
use App\Http\Requests\StoreUploadRequest;

class UploadController extends Controller
{
    public function index()
    {
        return view('index');
    }
    public function store(StoreUploadRequest $request)
    {
        DirectoryHelper::createDirectoryIfNotExists( __CLASS__, __FUNCTION__);

        $file = $request->file('excel_file');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->storeAs('import', $fileName, 'public');

        return redirect()->back()->with('success', 'Arquivo de cobrança enviado com sucesso!');
    }
}
