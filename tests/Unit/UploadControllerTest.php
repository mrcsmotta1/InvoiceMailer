<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Services\ExcelService;
use App\Services\SendBarcodeMail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreUploadRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\UploadController;
use App\Repositories\EloquentInvoiceRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UploadControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function testUploadFileMustBeRequired()
    {

        $request = new Request([]);
        $response = $this->post('/', $request->all());

        $rules = [
            'excel_file' => 'required|file|mimes:xls,xlsx,csv'
        ];

        $messages = [
            'excel_file.required' => 'Por favor, selecione um arquivo com extensão: xls, xlsx ou csv para upload.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        $response->assertStatus(302);

        $this->assertTrue($validator->fails());
        $this->assertEquals(
            'Por favor, selecione um arquivo com extensão: xls, xlsx ou csv para upload.',
            $validator->errors()->first('excel_file')
        );
    }

    /**
     * @test
     */
    public function testUploadMustInvalidateTheFileExtension()
    {
        $file = UploadedFile::fake()->create('arquivo.txt', 1024);
        $request = new Request([
            'excel_file' => $file,
        ]);

        $response = $this->post('/', $request->all());


        $rules = [
            'excel_file' => 'required|file|mimes:xls,xlsx,csv'
        ];

        // Define as mensagens de erro personalizadas
        $messages = [
            'excel_file.mimes' => 'O arquivo deve ter uma extensão válida: xls, xlsx ou csv.',
        ];

        // Executa a validação
        $validator = Validator::make(['excel_file' => $file], $rules, $messages);

        $response->assertStatus(302);

        $this->assertTrue($validator->fails());
        $this->assertEquals(
            'O arquivo deve ter uma extensão válida: xls, xlsx ou csv.',
            $validator->errors()->first('excel_file')
        );
    }

    /**
     * @test
     */
    public function upload_csv_must_validate_the_file_extension()
    {
        Storage::fake('public');

        // Conteúdo do arquivo CSV
        $fileContent = [
            Invoice::factory()->make()->toArray()
        ];

        $filePath = 'test.csv';

        // Converte o conteúdo do arquivo para uma string CSV
        $fileContentString = '';
        foreach ($fileContent as $row) {
            $fileContentString .= implode(',', $row) . "\n";
        }

        // Cria um arquivo CSV falso com o conteúdo
        $file = UploadedFile::fake()->createWithContent($filePath, $fileContentString);

        // Mock do Request
        $request = Mockery::mock(StoreUploadRequest::class);
        $request->shouldReceive('file')->with('excel_file')->andReturn($file);

        // Mock dos Serviços
        $excelService = Mockery::mock(ExcelService::class);

        $invoice = Invoice::factory()->make()->toArray();
        $excelService->shouldReceive('processSpreadsheet')->andReturn([
            $invoice
        ]);

        $invoiceRepository = Mockery::mock(EloquentInvoiceRepository::class);
        $invoiceRepository->shouldReceive('add')->with(Mockery::on(function ($arg) use ($invoice) {
            // Verifica se o valor é um float
            return is_float($invoice['valor']);
        }));

        $invoiceRepository->shouldReceive('add')->with([
            $invoice
        ]);

        $sendBarcodeMail = new SendBarcodeMail();
        $barcode = $sendBarcodeMail->generatorBarCode($invoice['codigo_barra']);
        $qrCode  = $sendBarcodeMail->generateQrCode($invoice['codigo_barra']);
        $text    = $sendBarcodeMail->expiredTicket($invoice['data_vencimento'], $invoice['valor'], $invoice['juros']);

        $sendBarcodeMail = Mockery::mock(SendBarcodeMail::class);
        $sendBarcodeMail->shouldReceive('sendEmail')->with([
            [
                $invoice['nome_fantasia'],
                $invoice['email'],
                $invoice['cnpj'],
                $invoice['codigo_barra'],
                $text,
                $invoice['data_vencimento'],
                $invoice['juros'],
                $barcode,
                $qrCode
            ]
        ], $request);

        $sendBarcodeMail->shouldReceive('sendEmail')
            ->with(Mockery::type('array'), Mockery::type(StoreUploadRequest::class))
            ->andReturn(true);

        Queue::fake();

        // Instanciar o Controller com os mocks
        $controller = new UploadController($excelService, $invoiceRepository, $sendBarcodeMail);

        // Chamar o método store
        $response = $controller->store($request);

        // Verificar a resposta
        $this->assertNotNull($response);
        // Adicione mais asserções conforme necessário
    }

    /**
     * @test
     */
    public function testUploadXlsxMustValidateTheFileExtension()
    {
        Storage::fake('public');
        $fileContent =  [
            Invoice::factory()->make()->toArray()
        ];

        $filePath = 'test.xlsx';

        $fileContentString = '';
        foreach ($fileContent as $row) {
            $fileContentString .= implode(',', $row) . "\n";
        }

        $file = UploadedFile::fake()->createWithContent($filePath, $fileContentString);

        // Mock do Request
        $request = Mockery::mock(StoreUploadRequest::class);
        $request->shouldReceive('file')->with('excel_file')->andReturn($file);

        // Mock dos Serviços
        $excelService = Mockery::mock(ExcelService::class);

        $invoice = Invoice::factory()->make()->toArray();
        $excelService->shouldReceive('processSpreadsheet')->andReturn([
            $invoice
        ]);

        $invoiceRepository = Mockery::mock(EloquentInvoiceRepository::class);
        $invoiceRepository->shouldReceive('add')->with(Mockery::on(function ($arg) use ($invoice) {
            // Verifica se o valor é um float
            return is_float($invoice['valor']);
        }));

        $invoiceRepository->shouldReceive('add')->with([
            $invoice
        ]);

        $sendBarcodeMail = new SendBarcodeMail();
        // dd($sendBarcodeMail);
        $barcode = $sendBarcodeMail->generatorBarCode($invoice['codigo_barra']);
        $qrCode  = $sendBarcodeMail->generateQrCode($invoice['codigo_barra']);
        $text    = $sendBarcodeMail->expiredTicket($invoice['data_vencimento'], $invoice['valor'], $invoice['juros']);

        $sendBarcodeMail = Mockery::mock(SendBarcodeMail::class);
        $sendBarcodeMail->shouldReceive('sendEmail')->with([
            [
                $invoice['nome_fantasia'],
                $invoice['email'],
                $invoice['cnpj'],
                $invoice['codigo_barra'],
                $text,
                $invoice['data_vencimento'],
                $invoice['juros'],
                $barcode,
                $qrCode
            ]
        ], $request);

        $sendBarcodeMail = Mockery::mock(SendBarcodeMail::class);
        $sendBarcodeMail->shouldReceive('sendEmail')
            ->with(Mockery::type('array'), Mockery::type(StoreUploadRequest::class))
            ->andReturn(true);


        Queue::fake();
        // Instanciar o Controller com os mocks
        $controller = new UploadController($excelService, $invoiceRepository, $sendBarcodeMail);

        // Chamar o método store
        $response = $controller->store($request);

        // Verificar a resposta
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('Arquivo de cobrança enviado com sucesso!', session('success'));
    }
}