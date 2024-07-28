@extends('admin.layouts.app')

@section('title', 'Upload de Arquivo')

@section('header')
  <h1 class="text-center">Upload de arquivo</h1>
@endsection

@if ($errors->any())
  <div class="alert alert-danger">
    <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

@section('content')
  <div class="container">
    <div class="card">
      <div class="card-body">
        <form action="{{ route('upload.excel') }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div class="form-group">
            <div class="custom-file text-left">
              <input type="file" name="excel_file" class="custom-file-input" id="file" accept=".xls,.xlsx,.csv">
              <label class="custom-file-label" for="customFile">Selecione o Arquivo</label>
            </div>
          </div>

          <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
      </div>
    </div>
  </div>
@endsection

@if (session('success'))
  <div class="alert alert-success">
    {{ session('success') }}
  </div>
@endif

@section('footer')
  <footer class="footer mt-5 py-3 bg-light">
    <div class="container">
      <div class="row">
        <div class="col-md-6 text-left">
          <p class="mb-0">©2024 MPMOTTA - Todos os direitos reservados.</p>
        </div>
        <div class="col-md-6 text-right">
          <p class="mb-0">Desenvolvido por <a href="https://www.linkedin.com/in/marcos-pedroso-motta/"
              target="_blank">Marcos Motta</a></p>
        </div>
      </div>
    </div>
  </footer>
@endsection
