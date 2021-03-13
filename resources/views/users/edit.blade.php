@extends('layouts.app')

@section('title', __('Edit User'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card  card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ __('Information') }}
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="">
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="table-responsive p-0">
                        <div class="card-body user-card-body">
                            <div class="row invoice-info">
                                <div class="col-sm-4 invoice-col">
                                    <div>
                                        <strong>{{ __('Name') }}: </strong>{{ $user->name }}<br/>
                                        <strong>{{ __('Email') }}: </strong>{{ $user->email }}<br/>
                                        <strong>{{ __('Mobile') }}: </strong>{{ $user->mobile }}<br/>
                                    </div>
                                </div>
                                <div class="col-sm-4 invoice-col">
                                    <div>
                                        @foreach($user->roles as $role)
                                            <strong>{{ __('Role(s)') }}: </strong>
                                            <span class='badge {{ Render::badgeRoleColor($role) }}'>{{ $role->name }}</span>
                                        @endforeach
                                        <br/>
                                        <strong>{{ __('Registered at') }}: </strong>
                                            {{ \Carbon\Carbon::parse($user->created_at)->format('Y/m/d H:i') }}<br/>
                                        <strong>{{ __('Last modified at') }}: </strong>
                                        {{ \Carbon\Carbon::parse($user->updated_at)->format('Y/m/d H:i') }}<br/>
                                    </div>
                                </div>
                                <div class="col-sm-4 invoice-col">
                                    <div>
                                        <strong>{{ __('Balance') }}: </strong>{{ $user->balance }} CAD<br/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card card-outline">
                    <form class="form-horizontal" method="post" action="{{ route('users.update', ['user' => $user->id]) }}"
                          enctype="multipart/form-data">
                        @csrf
                        @method('patch')
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ __('Edit') }}
                        </h3>
                        <div class="float-right">
                            <input type="checkbox" name="active" data-bootstrap-switch
                                   data-on-text="Active" data-off-text="Inactive"
                                   data-on-color="success" data-label-width="10"
                                   {{ $user->active ==  1 ? 'checked' : ''}}>
                        </div>
                    </div>
                    <!-- /.card-header -->
                        <div class="card-body user-card-body">
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label for="name">{{ __('Name') }}</label>
                                    <input type="text" name="name" id="name"
                                           class="form-control {{ Render::isInvalid('name') }}"
                                           placeholder="{{ __('Name') }}"
                                           value="{{ old('name') ?: $user->name }}"
                                        >
                                    {!! \Render::errMsg('name') !!}
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="email">{{ __('Email') }}</label>
                                    <input type="email" name="email" id="email"
                                           class="form-control {{ Render::isInvalid('email') }}"
                                           placeholder="{{ __('Enter Email') }}"
                                           value="{{ old('email') ?: $user->email }}"
                                        >
                                    {!! \Render::errMsg('email') !!}
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="roles" title="{{ __('Company/Person Name') }}">{{ __('Role(s)') }}</label>
                                    <select name="roles" class="form-control selectpicker {{ Render::isInvalid('roles') }}" id="roles">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}"
                                                    @if(in_array($role->id, $user->roles()->pluck('id')->toArray()))
                                                        selected
                                                    @endif
                                            >
                                                {{ __($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    {!! \Render::errMsg('roles') !!}
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="password">{{ __('Password') }}</label>
                                        <input type="password" name="password" id="password"
                                               class="form-control {{ Render::isInvalid('password') }}"
                                               placeholder="{{ __('Enter New Password') }}"
                                        >
                                        {!! \Render::errMsg('password') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label for="balance">{{ __('Balance') }}</label>
                                    <input type="number" name="balance" id="balance"
                                           class="form-control {{ Render::isInvalid('balance') }}"
                                           placeholder="{{ __('Balance') }}"
                                           value="{{ old('balance') ?: $user->balance }}"
                                    >
                                    {!! \Render::errMsg('balance') !!}
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
