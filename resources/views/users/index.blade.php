@extends('layouts.app')

@section('title', __('Users'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('All Users') }}</span>
                        <span class="info-box-number">{{ $allCount }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-check"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('Active Users') }}</span>
                        <span class="info-box-number">{{ $activeCount }}</span>
                    </div>
                </div>
            </div>
            <!-- fix for small devices only -->
            <div class="clearfix hidden-md-up"></div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-user-times"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('Inactive Users') }}</span>
                        <span class="info-box-number">{{ $inactiveCount }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-user-friends"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('Buyers/Sellers') }}</span>
                        <span class="info-box-number">{{ $userRoleCount }}</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-12">
                <div class="card card-outline">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Filter') }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="">
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="table-responsive p-0">
                        <form role="form" method="get" action="{{ route('users.index') }}">
                            <div class="card-body user-card-body filter-font">
                                <div class="row">
                                    <div class="form-group col-md-3 col-sm-3 col-12">
                                        <label for="roles" title="{{ __('Company/Person Name') }}">{{ __('Role(s)') }}</label>
                                        <select name="roles[]" class="form-control selectpicker" id="roles" multiple>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}" @if(@in_array($role->id, request()->query('roles'))) selected @endif>
                                                    {{ __($role->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-3 col-12">
                                        <label for="email" title="{{ __('Company/Person ID') }}">{{ __('Email') }}</label>
                                        <input type="text" name="email" class="form-control" id="email"
                                               value="{{ request()->query('email') }}" placeholder="{{ __('Email') }}">
                                    </div>
                                    <div class="form-group col-md-12 col-sm-12 col-12">
                                        <div class="">
                                            <button type="submit" class="btn btn-outline-primary">{{ __('Search') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->

                        </form>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card card-outline user-paginate">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('List') }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="">
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap table-striped">
                            <thead>
                            <tr>
                                <th>{{ __('#ID') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Mobile') }}</th>
                                <th>{{ __('Role(s)') }}</th>
                                <th>{{ __('Active') }}</th>
                                <th>{{ __('Operations') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->mobile }}</td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class='badge {{ Render::badgeRoleColor($role) }}'>{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>{!! $user->isActive() ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                                    @can('users-update')
                                        <td>
                                            <a href="{{ route('users.edit', $user->id) }}">{{ __('Edit') }}</a>
                                        </td>
                                    @endcan()
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer d-flex align-items-center justify-content-center">
                        {{ $users->withQueryString()->links() }}
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
@endsection
