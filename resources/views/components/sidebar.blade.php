<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('dist/img/user.svg') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">Sasanka Deshapriya</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item {{ request()->is('/') ? 'active' : '' }}">
                    <a href="/" class="nav-link">
                        <i class="nav-icon fas fa-th"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li
                    class="nav-item has-treeview {{ request()->is('product*') || request()->is('add-product') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('product*') || request()->is('add-product') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>
                            Product
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ request()->is('product*') || request()->is('add-product') ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="{{ route('product.add') }}"
                                class="nav-link {{ request()->is('add-product') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Product</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('product.manage') }}"
                                class="nav-link {{ request()->is('product') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Product</p>
                            </a>
                        </li>
                    </ul>
                </li>
                 <!-- Vehicle Section -->
                 <li
                    class="nav-item has-treeview {{ request()->is('vehicle*') || request()->is('add-vehicle') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('vehicle*') || request()->is('add-vehicle') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>
                            Vehicle
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ request()->is('vehicle*') || request()->is('add-vehicle') ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="{{ route('vehicle.add') }}"
                                class="nav-link {{ request()->is('add-vehicle') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Vehicle</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('vehicle.manage') }}"
                                class="nav-link {{ request()->is('vehicle') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Vehicle</p>
                            </a>
                        </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
