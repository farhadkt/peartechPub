@extends('layouts.app')

@section('title', __('Update Settings'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ __('Settings List') }}
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="">
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" method="post" action="{{ route('settings.update') }}">
                            @csrf
                            @method('patch')
                            <div class="form-group col-md-3">
                                <label for="name">{{ __('Commission') }}</label>
                                <input type="text" name="commission" id="commission"
                                       class="form-control {{ Render::isInvalid('commission') }}"
                                       placeholder="{{ __('Commission') }}"
                                       value="{{ old('commission') ?: $setting['commission'] }}"
                                >
                                {!! Render::errMsg('commission') !!}
                            </div>
                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Update
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
