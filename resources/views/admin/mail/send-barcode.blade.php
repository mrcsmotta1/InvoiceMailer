@component('mail::message')

Prezada Empresa: **{{ $nome_fantasia }}**.

Segue o código de barras: **{{ $codigo_barra }}** para pagamento no dia **{{ $data_vencimento }}**, {{ $text }} conforme dados da
planilha.


{!! $barcode !!}
Código de barras


<img src="data:image/png;base64, {!!  $qrCode !!} ">

QR Code não é válido para PIX!


@endcomponent