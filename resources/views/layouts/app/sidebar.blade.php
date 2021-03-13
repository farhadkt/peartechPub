<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link">
        <img src="{{ asset('img/PearTech.png') }}" alt="pear tech" class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <img src="{{ asset('img/PearTechText.png') }}" alt="pear tech" class="brand-text"
             style="opacity: .8">
        {{--<span class="brand-text font-weight-light">PearTech</span>--}}
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mb-3 d-flex">
            <div class="image menu-img">
                <img src="{{ asset('img/default-user.png') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->name }}</a>
                <span class="user-cash">Balance: {{ number_format(auth()->user()->balance, 2) }} CAD</span>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('dashboard.index') }}"
                       class="nav-link {{ Render::navActive('dashboard.index') }} {{ Render::navActive('home') }}">
                        <i class="nav-icon fas fa-columns"></i>
                        <p>
                            {{ __('Dashboard') }}
                        </p>
                    </a>
                </li>
                @canany('users-index', 'users-create', 'users-update', 'users-destroy')
                    <li class="nav-item has-treeview {{ Render::navTreeOpen('users.') }}">
                        <a class="nav-link {{ Render::navTreeActive('users.') }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                {{ __('Users') }}
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        @can('users-index')
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('users.index') }}"
                                       class="nav-link {{ Render::navActive('users.index') }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ __('All Users') }}</p>
                                    </a>
                                </li>
                            </ul>
                        @endcan
                        @can('users-create')
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('users.create') }}"
                                       class="nav-link {{ Render::navActive('users.create') }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ __('Create') }}</p>
                                    </a>
                                </li>
                            </ul>
                        @endcan
                    </li>
                @endcanany
                @canany('products-import')
                    <li class="nav-item has-treeview {{ Render::navTreeOpen('products.') }}">
                        <a class="nav-link {{ Render::navTreeActive('products.') }}">
                            <i class="nav-icon fas fa-box"></i>
                            <p>
                                {{ __('Products') }}
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        @can('users-index')
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('products.import.form') }}"
                                       class="nav-link {{ Render::navActive('products.import.form') }}">
                                        <i class="nav-icon fas fa-file-import"></i>
                                        <p>{{ __('Import') }}</p>
                                    </a>
                                </li>
                            </ul>
                        @endcan
                    </li>
                @endcanany
                <li class="nav-item">
                    <a href="{{ route('transactions.index') }}"
                       class="nav-link {{ Render::navActive('transactions.index') }} {{ Render::navActive('transactions.index') }}">
                        <i class="nav-icon fas fa-search-dollar"></i>
                        <p>
                            {{ __('Transactions') }}
                        </p>
                    </a>
                </li>
                @canany('users-index', 'users-create', 'users-update', 'users-destroy')
                    <li class="nav-item">
                        <a href="{{ route('settings.edit') }}"
                           class="nav-link {{ Render::navActive('settings.edit') }} {{ Render::navActive('settings.edit') }}">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>
                                {{ __('Settings') }}
                            </p>
                        </a>
                    </li>
                @endcanany
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
