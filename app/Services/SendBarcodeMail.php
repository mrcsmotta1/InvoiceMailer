<?php

namespace App\Services;

use DateTime;
use App\Mail\SendBarcode;
use Illuminate\Support\Facades\Mail;
use Picqer\Barcode\BarcodeGeneratorHTML;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class SendBarcodeMail
{
    public function sendEmail(array $dados): void
    {
        foreach ($dados as $key => $value) {
            $dataVencimento = $value['data_vencimento'];
            $juros          = $value['juros'];
            $dataVencimento = $value['data_vencimento'];
            $valor          = $value['valor'];

            $barcode = $this->generatorBarCode($value['codigo_barra']);
            $qrCode  = $this->generateQrCode($value['codigo_barra']);
            $text    = $this->expiredTicket($dataVencimento, $valor, $juros);

            $email = new SendBarcode(
                $value['nome_fantasia'],
                $value['email'],
                $value['cnpj'],
                $value['codigo_barra'],
                $text,
                $dataVencimento,
                $juros,
                $barcode,
                $qrCode,
            );
            Mail::to($value['email'])->queue($email);
        }
    }

    public function generatorBarCode(string $line): string
    {
        $codigo_barra_leitura = preg_replace('/[\.\s]/', '', $line);

        $generator = new BarcodeGeneratorHTML();
        $barcode = $generator->getBarcode($codigo_barra_leitura, $generator::TYPE_CODE_128, 2, 100);

        return $barcode;
    }

    public function generateQrCode(string $line): string
    {
        $codigo_barra_leitura = preg_replace('/[\.\s]/', '', $line);
        $qrCode = base64_encode(QrCode::format('png')->size(256)->generate($codigo_barra_leitura));

        return $qrCode;
    }

    public function expiredTicket(string $dataVencimento, string $valor, string $juros): string
    {
        $dateTicket = DateTime::createFromFormat('d/m/y', $dataVencimento)->format('Y-m-d');

        $dateNow = date('Y-m-d');

        $isExpired = (strtotime($dateTicket) < strtotime($dateNow)) ? true : false;

        $data = null;
        $valorFormated = number_format($valor, 2, ",", ".");
        $data          = "no valor de **R$" . $valorFormated . "**";

        if ($isExpired) {
            $juros = str_replace("%", "", $juros) / 100;
            $amountWithInterest = $valor + ($valor * $juros);
            $valorFormated      = number_format($amountWithInterest, 2, ",", ".");
            $data               = "boleto vencido, valor com juros de **R$" . $valorFormated . "**";
        }

        return $data;
    }
}
