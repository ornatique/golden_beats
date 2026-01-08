<!-- <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ url('/admin/dashboard') }}" class="brand-link">
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside> -->

<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="{{ url('/admin/dashboard') }}" class="brand-link">
    <!-- <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> -->
    <span class="brand-text font-weight-light">Golden Beads Admin</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
        <li class="nav-item">
          <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              Dashboard

            </p>
          </a>
        </li>
        @can('role_perm_sidebar')
        <li class="nav-item 
        {{ request()->routeIs('admin.permissions.*', 'admin.roles.*') ? 'menu-open' : '' }}">

          <a href="#" class="nav-link 
        {{ request()->routeIs('admin.permissions.*', 'admin.roles.*') ? 'active' : '' }}">

            <i class="nav-icon fas fa-user-shield"></i>
            <p>
              Roles & Permission
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>

          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('admin.permissions.index') }}"
                class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog nav-icon"></i>
                <p>Permission</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{ route('admin.roles.index') }}"
                class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <i class="fas fa-key nav-icon"></i>
                <p>Roles</p>
              </a>
            </li>
          </ul>
        </li>
        @endcan

        @can('user-view')
        @php
        $isUserMenu =
        request()->routeIs('admin.users.*') &&
        request('only') === 'user';
        @endphp

        <li class="nav-item {{ $isUserMenu ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ $isUserMenu ? 'active' : '' }}">
            <i class="nav-icon fas fa-user"></i>
            <p>
              Users
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>

          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('admin.users.index', ['only' => 'user']) }}"
                class="nav-link {{ $isUserMenu ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Users List</p>
              </a>
            </li>
          </ul>
        </li>
        @endcan

        @can('customer-view')
        @php
        $isCustomerMenu =
        request()->routeIs('admin.users.*') &&
        request('only') === 'customer';
        @endphp

        <li class="nav-item {{ $isCustomerMenu ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ $isCustomerMenu ? 'active' : '' }}">
            <i class="nav-icon fas fa-user-tag"></i>
            <p>
              Customers
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>

          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('admin.users.index', ['only' => 'customer', 'status' => 'active']) }}"
                class="nav-link {{ request('only') === 'customer' && request('status') === 'active' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>All Customer</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{ route('admin.users.index', ['only' => 'customer', 'status' => 'inactive']) }}"
                class="nav-link {{ request('only') === 'customer' && request('status') === 'inactive' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>New Customer</p>
              </a>
            </li>
          </ul>
        </li>
        @endcan

        @php
        $productMenuOpen =
        request()->routeIs('admin.categories.*') ||
        request()->routeIs('admin.subcategories.*') ||
        request()->routeIs('admin.products.*');
        @endphp

        <li class="nav-item {{ $productMenuOpen ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ $productMenuOpen ? 'active' : '' }}">
            <i class="nav-icon fas fa-boxes"></i>
            <p>
              Product Management
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>

          <ul class="nav nav-treeview">

            {{-- Category --}}
            <li class="nav-item">
              <a href="{{ route('admin.categories.index') }}"
                class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="fas fa-tag nav-icon"></i>
                <p>Category</p>
              </a>
            </li>

            {{-- Sub Category --}}
            <li class="nav-item">
              <a href="{{ route('admin.subcategories.index') }}"
                class="nav-link {{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}">
                <i class="fas fa-tags nav-icon"></i>
                <p>Sub Category</p>
              </a>
            </li>

            {{-- Product --}}
            <li class="nav-item">
              <a href="{{ route('admin.products.index') }}"
                class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i class="fas fa-box-open nav-icon"></i>
                <p>Product</p>
              </a>
            </li>

          </ul>
        </li>
        @php
        $estimateMenuOpen =
        request()->routeIs('admin.orders.*')||
        request()->routeIs('admin.custom-orders.*') 
       
        @endphp

        <li class="nav-item {{ $estimateMenuOpen ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ $estimateMenuOpen ? 'active' : '' }}">
            <i class="nav-icon fas fa-file-invoice-dollar"></i>
            <p>
              Estimate Management
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>

          <ul class="nav nav-treeview">

            {{-- Order --}}
            <li class="nav-item">
              <a href="{{ route('admin.orders.index') }}"
                class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart nav-icon"></i>
                <p>Orders</p>
              </a>
            </li>

            {{-- Custom Order --}}
            <li class="nav-item">
              <a href="{{ route('admin.custom-orders.index') }}"
                class="nav-link {{ request()->routeIs('admin.custom-orders.*') ? 'active' : '' }}">
                <i class="fas fa-pencil-ruler nav-icon"></i>
                <p>Custom Orders</p>
              </a>
            </li>

          </ul>
        </li>

      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>