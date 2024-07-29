<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendBarcode extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $nome_fantasia,
        public string $email,
        public string $cnpj,
        public string $codigo_barra,
        public string $text,
        public string $data_vencimento,
        public string $juros,
        public string $barcode,
        public string $qrCode,
    )
    {

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Envio de código de barra',
        );
    }

    /**
     * Get the message content definition.
     */
    public function build()
     {
        return $this->markdown('admin.mail.send-barcode');
     }

}
