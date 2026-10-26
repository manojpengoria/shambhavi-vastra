<div class="page-sidebar navbar-collapse collapse">
    <!-- BEGIN SIDEBAR MENU -->
    <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
    <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
    <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
    <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
    <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
    <ul class="page-sidebar-menu   " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
        <li class="nav-item active">
            <a href="{{ route('admin.dashboard') }}" class="nav-link nav-toggle">
                <i class="icon-home"></i>
                <span class="title">Dashboard</span>
            </a>
        </li>
        @canany(['roles.view', 'permissions.view', 'users.view', 'countries.view', 'states.view', 'cities.view', 'postal-codes.view', 'areas.view', 'warehouses.view', 'courier-partners.view', 'shipping-methods.view', 'shipping-zones.view', 'shipping-packages.view', 'shipping-classes.view', 'shipping-settings.view'])
        <li class="heading">
            <h3 class="uppercase">Masters</h3>
        </li>
        @can('roles.view')
        <li class="nav-item  ">
            <a href="{{ route('admin.roles.index') }}" class="nav-link nav-toggle">
                <i class="icon-badge"></i>
                <span class="title">Roles & Permissions</span>
            </a>
        </li>
        @endcan
        @can('permissions.view')
        <li class="nav-item  ">
            <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-key"></i>
                <span class="title">Permissions</span>
            </a>
        </li>
        @endcan
        @canany(['countries.view', 'states.view', 'cities.view', 'postal-codes.view', 'areas.view'])
        <li class="nav-item {{ request()->routeIs('admin.countries.*', 'admin.states.*', 'admin.cities.*', 'admin.postal-codes.*', 'admin.areas.*') ? 'active open' : '' }}">
            <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-pointer"></i>
                <span class="title">Location Management</span>
                <span class="arrow"></span>
            </a>
            <ul class="sub-menu">
                @can('countries.view')
                <li class="nav-item {{ request()->routeIs('admin.countries.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.countries.index') }}" class="nav-link ">
                        <span class="title">Countries</span>
                    </a>
                </li>
                @endcan
                @can('states.view')
                <li class="nav-item {{ request()->routeIs('admin.states.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.states.index') }}" class="nav-link ">
                        <span class="title">States</span>
                    </a>
                </li>
                @endcan
                @can('cities.view')
                <li class="nav-item {{ request()->routeIs('admin.cities.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.cities.index') }}" class="nav-link ">
                        <span class="title">Cities</span>
                    </a>
                </li>
                @endcan
                @can('postal-codes.view')
                <li class="nav-item {{ request()->routeIs('admin.postal-codes.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.postal-codes.index') }}" class="nav-link ">
                        <span class="title">Postal Codes</span>
                    </a>
                </li>
                @endcan
                @can('areas.view')
                <li class="nav-item {{ request()->routeIs('admin.areas.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.areas.index') }}" class="nav-link ">
                        <span class="title">Areas</span>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcanany
        <li class="nav-item  {{ request()->routeIs('admin.variant-type.*', 'admin.variant-values.*') ? 'active' : '' }}">
            <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-vector"></i>
                <span class="title">Variants</span>
                <span class="arrow"></span>
            </a>
            <ul class="sub-menu">
                <li class="nav-item  ">
                    <a href="{{ route('admin.variant-type.index') }}" class="nav-link ">
                        <span class="title">Variant Types</span>
                    </a>
                </li>
                <li class="nav-item  ">
                    <a href="{{ route('admin.variant-values.index') }}" class="nav-link ">
                        <span class="title">Variant Values</span>
                    </a>
                </li>
            </ul>
        </li>
        @canany(['warehouses.view', 'courier-partners.view', 'shipping-methods.view', 'shipping-zones.view', 'shipping-packages.view', 'shipping-classes.view', 'shipping-settings.view'])
        <li class="nav-item {{ request()->routeIs('admin.warehouses.*', 'admin.courier-partners.*', 'admin.shipping-methods.*', 'admin.shipping-zones.*', 'admin.shipping-packages.*', 'admin.shipping-classes.*', 'admin.shipping-settings.*') ? 'active open' : '' }}">
            <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-plane"></i>
                <span class="title">Shipping</span>
                <span class="arrow"></span>
            </a>
            <ul class="sub-menu">
                <small style="padding: 9px 14px 9px 30px; color: #999;">SETUP</small>
                @can('warehouses.view')
                <li class="nav-item {{ request()->routeIs('admin.warehouses.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.warehouses.index') }}" class="nav-link ">
                        <span class="title">Warehouses</span>
                    </a>
                </li>
                @endcan
                @can('courier-partners.view')
                <li class="nav-item {{ request()->routeIs('admin.courier-partners.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.courier-partners.index') }}" class="nav-link ">
                        <span class="title">Courier partners</span>
                    </a>
                </li>
                @endcan
                @can('shipping-methods.view')
                <li class="nav-item {{ request()->routeIs('admin.shipping-methods.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.shipping-methods.index') }}" class="nav-link ">
                        <span class="title">Shipping methods</span>
                    </a>
                </li>
                @endcan
                @can('shipping-zones.view')
                <li class="nav-item {{ request()->routeIs('admin.shipping-zones.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.shipping-zones.index') }}" class="nav-link ">
                        <span class="title">Shipping zones</span>
                    </a>
                </li>
                @endcan
                @can('shipping-packages.view')
                <li class="nav-item {{ request()->routeIs('admin.shipping-packages.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.shipping-packages.index') }}" class="nav-link ">
                        <span class="title">Packages</span>
                    </a>
                </li>
                @endcan
                @can('shipping-classes.view')
                <li class="nav-item {{ request()->routeIs('admin.shipping-classes.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.shipping-classes.index') }}" class="nav-link ">
                        <span class="title">Shipping classes</span>
                    </a>
                </li>
                @endcan
                <small style="padding: 9px 14px 9px 30px; color: #999;">RATES & COVERAGE</small>
                <li class="nav-item  ">
                    <a href="" class="nav-link ">
                        <span class="title">Pincode serviceability</span>
                    </a>
                </li>
                <li class="nav-item  ">
                    <a href="" class="nav-link ">
                        <span class="title">Shipping rates</span>
                    </a>
                </li>
                <small style="padding: 9px 14px 9px 30px; color: #999;">CONFIG</small>
                @can('shipping-settings.view')
                <li class="nav-item {{ request()->routeIs('admin.shipping-settings.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.shipping-settings.edit') }}" class="nav-link ">
                        <span class="title">Shipping settings</span>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcanany
        @endcanany 
        @can('categories.view')
        <li class="nav-item  {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <a href="{{ route('admin.categories.index') }}" class="nav-link nav-toggle">
                <i class="icon-list"></i>
                <span class="title">Categories</span>
            </a>
        </li>
        @endcan
        <li class="heading">
            <h3 class="uppercase">Features</h3>
        </li>
        @can('products.view')
        <li class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <a href="{{ route('admin.products.index') }}" class="nav-link nav-toggle">
                <i class="icon-graph"></i>
                <span class="title">Products</span>
            </a>
        </li>
        @endcan
        <li class="nav-item  ">
            <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-basket"></i>
                <span class="title">Orders</span>
            </a>
        </li>
        @can('users.view')
        <li class="nav-item  ">
            <a href="{{ route('admin.users.index') }}" class="nav-link nav-toggle">
                <i class="icon-users"></i>
                <span class="title">Admin Users</span>
            </a>
        </li>
        @endcan
    </ul>
    <!-- END SIDEBAR MENU -->
</div>