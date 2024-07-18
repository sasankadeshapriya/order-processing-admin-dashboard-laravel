<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex" data-toggle="modal" data-target="#userPanelModal">
            <div class="image">
                <img src="{{ asset('dist/img/user.svg') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a class="d-block">{{ session('email') ?? 'No User Signed In' }}</a>
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
                    </ul>
                </li>

                <!-- Invoices Section -->
                <li class="nav-item has-treeview {{ request()->is('invoices') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('invoices') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Invoices <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ request()->is('invoices') ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="{{ route('invoices.show') }}"
                                class="nav-link {{ request()->is('invoices') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Invoices</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Payments Section -->
                <li
                    class="nav-item has-treeview {{ request()->is('payment*') || request()->is('all-payments') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('payment*') || request()->is('all-payments') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-check-alt"></i>
                        <p>Payments <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ request()->is('payment*') || request()->is('all-payments') ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="{{ route('payment.manage') }}"
                                class="nav-link {{ request()->is('payment') && !request()->is('all-payments') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Verify Cheques</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('payment.all') }}"
                                class="nav-link {{ request()->is('all-payments') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Payments</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Employee Section -->
                <li
                    class="nav-item has-treeview {{ request()->is('employee') || request()->is('employee/*') || request()->is('add-employee') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('employee') || request()->is('employee/*') || request()->is('add-employee') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>Employee <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ request()->is('employee') || request()->is('employee/*') || request()->is('add-employee') ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="{{ route('employee.add') }}"
                                class="nav-link {{ request()->is('add-employee') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Employee</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('employee.manage') }}"
                                class="nav-link {{ request()->is('employee') && !request()->is('employee/*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Employee</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- client Section -->
                <li
                    class="nav-item has-treeview {{ request()->is('client') || request()->is('client/*') || request()->is('add-client') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('client') || request()->is('client/*') || request()->is('add-client') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Client <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ request()->is('client') || request()->is('client/*') || request()->is('add-client') ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="{{ route('client.add') }}"
                                class="nav-link {{ request()->is('add-client') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Client</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('client.manage') }}"
                                class="nav-link {{ request()->is('client') && !request()->is('client/*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Clients</p>
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
                            <a href="{{ route('emp.tracking') }}"
                                class="nav-link {{ request()->is('tracking') ? 'active' : '' }}">
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

                <!-- Reports Section -->
                <li
                    class="nav-item has-treeview {{ request()->is('report*') || request()->is('sales-report') || request()->is('outstanding-report') || request()->is('commission-report') || request()->is('day-end-report') || request()->is('mrp-report') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('report*') || request()->is('sales-report') || request()->is('outstanding-report') || request()->is('commission-report') || request()->is('day-end-report') || request()->is('mrp-report') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>Reports <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ request()->is('report*') || request()->is('sales-report') || request()->is('outstanding-report') || request()->is('commission-report') || request()->is('day-end-report') || request()->is('mrp-report') ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="{{ route('sales.show') }}"
                                class="nav-link {{ request()->is('sales-report') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Sales Report</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('outstanding.show') }}"
                                class="nav-link {{ request()->is('outstanding-report') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Outstanding Report</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('commission.show') }}"
                                class="nav-link {{ request()->is('commission-report') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Commission Report</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('day-end.show') }}"
                                class="nav-link {{ request()->is('day-end-report') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Day End Report</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('mrp-report.show') }}"
                                class="nav-link {{ request()->is('mrp-report') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>MRP Report</p>
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

<!-- User Panel Modal -->
<div class="modal fade" id="userPanelModal" tabindex="-1" role="dialog" aria-labelledby="userPanelModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userPanelModalLabel">User Settings</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Change Password Form -->
                <form id="changePasswordForm">
                    @csrf
                    <input type="hidden" name="email" value="{{ session('email') }}">
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password"
                            required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </form>
                <hr>
                <!-- Delete Account Form -->
                <form id="deleteAccountForm">
                    @csrf
                    <div class="form-group">
                        <label for="emailValidation">Enter Email to Confirm</label>
                        <input type="email" class="form-control" id="emailValidation" name="email" required>
                    </div>
                    <button type="submit" class="btn btn-danger">Delete Account</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (newPassword !== confirmPassword) {
            toastr.error('Passwords do not match!');
            return;
        }

        const formData = new FormData(this);
        try {
            const response = await fetch('{{ route('password.update') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                }
            });
            const result = await response.json();
            if (response.ok) {
                toastr.success('Password changed successfully!');
                window.location.href = '{{ route('logout') }}'; // Redirect after successful change
            } else {
                toastr.error(result.message || 'Error updating password.');
            }
        } catch (error) {
            console.error('Error:', error);
            toastr.error('Server error: Unable to change password.');
        }
    });

    document.getElementById('deleteAccountForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        try {
            const response = await fetch('{{ route('account.delete') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                }
            });
            const result = await response.json();
            if (response.ok) {
                toastr.success('User account deleted successfully!');
                window.location.href = '{{ route('logout') }}'; // Redirect after successful deletion
            } else {
                toastr.error(result.message || 'Error deleting account.');
            }
        } catch (error) {
            console.error('Error:', error);
            toastr.error('Server error: Unable to delete account.');
        }
    });
</script>
