@extends('layouts.app')

@section('title', __('Import product details'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ __('Import') }}
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="">
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" method="post" action="{{ route('products.import.parse') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group col-md-6">
                                <label for="exampleInputFile">CSV file to import</label>
                                <div class="custom-file">
                                    <input id="file" type="file" class="custom-file-input {{ Render::isInvalid('file') }}" name="file">
                                    <label class="custom-file-label">{{ __('Choose file') }}</label>
                                    {!! Render::errMsg('file') !!}
                                </div>
                            </div>
                            <div class="form-group form-check col-md-6 import-check" style="padding-left: 1.6rem;">
                                <input name="direct_import" type="checkbox" class="form-check-input" id="direct_import" checked>
                                <label class="form-check-label" for="direct_import">Import directly without preview</label>
                                <p style="font-size: 12px; color: red;"><strong>Important:</strong> Due to performance issue if the file has more than 1000 rows check this option.</p>
                            </div>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Parse CSV
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
