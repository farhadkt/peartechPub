@extends('layouts.app')

@section('title', __('Create User'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ __('Create') }}
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="">
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <form class="form-horizontal" method="post" action="{{ route('users.store') }}"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="card-body user-card-body">
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label for="name">{{ __('Name') }}</label>
                                    <input type="text" name="name" id="name"
                                           class="form-control {{ Render::isInvalid('name') }}"
                                           placeholder="{{ __('Name') }}"
                                           value="{{ old('name') }}"
                                    >
                                    {!! Render::errMsg('name') !!}
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="email">{{ __('Email') }}</label>
                                    <input type="email" name="email" id="email"
                                           class="form-control {{ Render::isInvalid('email') }}"
                                           placeholder="{{ __('Enter Email') }}"
                                           value="{{ old('email') }}"
                                    >
                                    {!! \Render::errMsg('email') !!}
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="roles" title="{{ __('Company/Person Name') }}">{{ __('Role(s)') }}</label>
                                    <select name="roles" class="form-control selectpicker {{ Render::isInvalid('roles') }}" id="roles">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}"
                                                    @if(in_array(old('roles'), $roles->toArray()))
                                                    selected
                                                @endif
                                            >
                                                {{ __($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    {!! \Render::errMsg('roles') !!}
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="mobile">{{ __('Mobile') }}</label>
                                    <input type="mobile" name="mobile" id="mobile"
                                           class="form-control {{ Render::isInvalid('mobile') }}"
                                           placeholder="{{ __('Enter Mobile') }}"
                                           value="{{ old('mobile') }}"
                                    >
                                    {!! \Render::errMsg('mobile') !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="password">{{ __('Password') }}</label>
                                        <input type="password" name="password" id="password"
                                               class="form-control {{ Render::isInvalid('password') }}"
                                               placeholder="{{ __('Enter Password') }}"
                                        >
                                        {!! \Render::errMsg('password') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer" style="text-align: center">
                            <button type="submit" class="btn btn-outline-primary" >
                                {{ __('Submit') }}
                            </button>
                        </div>
                        <!-- /.card-footer -->
                    </form>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
@endsection
