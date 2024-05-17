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

                <!--product-->
                <li
                    class="nav-item has-treeview {{ request()->is('product*') || request()->is('add-product') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('product*') || request()->is('add-product') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>Product <i class="right fas fa-angle-left"></i></p>
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

                <!--batch-->
                <li
                    class="nav-item has-treeview {{ request()->is('batch*') || request()->is('add-batch') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('batch*') || request()->is('add-batch') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>
                            Batch
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ request()->is('batch*') || request()->is('add-batch') ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="{{ route('batch.add') }}"
                                class="nav-link {{ request()->is('add-batch') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Batch</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('batch.manage') }}"
                                class="nav-link {{ request()->is('batch') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Batches</p>
                            </a>
                        </li>
                    </ul>
                </li>


                <!-- Vehicle Inventory -->
                <li
                    class="nav-item has-treeview {{ request()->is('vehicle-inventory') || request()->is('vehicle-inventory/*') || request()->is('add-vehicle-inventory') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('vehicle-inventory') || request()->is('vehicle-inventory/*') || request()->is('add-vehicle-inventory') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>Vehicle Inventory <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ request()->is('vehicle-inventory') || request()->is('vehicle-inventory/*') || request()->is('add-vehicle-inventory') ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="{{ route('vehicle-inventory.add') }}"
                                class="nav-link {{ request()->is('add-vehicle-inventory') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add to Inventory</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('vehicle.inventory') }}"
                                class="nav-link {{ request()->is('vehicle-inventory') && !request()->is('vehicle-inventory/*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Inventory</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('emp.tracking') }}" class="nav-link {{ request()->is('tracking') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Employee Tracking</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Vehicle Section -->
                <li
                    class="nav-item has-treeview {{ request()->is('vehicle') || request()->is('vehicle/*') || request()->is('add-vehicle') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('vehicle') || request()->is('vehicle/*') || request()->is('add-vehicle') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-car"></i>
                        <p>Vehicle <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ request()->is('vehicle') || request()->is('vehicle/*') || request()->is('add-vehicle') ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="{{ route('vehicle.add') }}"
                                class="nav-link {{ request()->is('add-vehicle') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Vehicle</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('vehicle.manage') }}"
                                class="nav-link {{ request()->is('vehicle') && !request()->is('vehicle/*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Vehicles</p>
                            </a>
                        </li>
                    </ul>
                </li>


                <!-- Assignments Section -->
                <li
                    class="nav-item has-treeview {{ request()->is('assignment*') || request()->is('add-assignment') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('assignment*') || request()->is('add-assignment') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tasks"></i>
                        <p>Assignments <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ request()->is('assignment*') || request()->is('add-assignment') ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="{{ route('assignment.add') }}"
                                class="nav-link {{ request()->is('add-assignment') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Assignment</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('assignment.manage') }}"
                                class="nav-link {{ request()->is('assignment') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Assignments</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('emp.tracking') }}" class="nav-link {{ request()->is('tracking') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Employee Tracking</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Routes Section -->
                <li
                    class="nav-item has-treeview {{ request()->is('route*') || request()->is('map') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('route*') || request()->is('map') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-route"></i>
                        <p>Routes <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ request()->is('route*') || request()->is('map') ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="{{ route('map') }}"
                                class="nav-link {{ request()->is('map') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Route</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('route.manage') }}"
                                class="nav-link {{ request()->is('route') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Routes</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <hr>
            <ul class="nav nav-pills nav-sidebar flex-column justify-content-end">
                <li class="nav-item">
                    <a href="{{ route('logout') }}" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
