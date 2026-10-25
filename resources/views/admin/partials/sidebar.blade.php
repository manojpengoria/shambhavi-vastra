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
        @canany(['roles.view', 'permissions.view', 'users.view'])
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
        <!-- <li class="nav-item  {{ request()->routeIs('admin.variant-type.*') ? 'active' : '' }}">
            <a href="{{ route('admin.variant-type.index') }}" class="nav-link nav-toggle">
                <i class="icon-list"></i>
                <span class="title">Variant Types</span>
            </a>
        </li>
        <li class="nav-item  {{ request()->routeIs('admin.variant-values.*') ? 'active' : '' }}">
            <a href="{{ route('admin.variant-values.index') }}" class="nav-link nav-toggle">
                <i class="icon-list"></i>
                <span class="title">Variant Values</span>
            </a>
        </li> -->
        <li class="nav-item  ">
            <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-pointer"></i>
                <span class="title">Location Management</span>
                <span class="arrow"></span>
            </a>
            <ul class="sub-menu">
                <li class="nav-item  ">
                    <a href="" class="nav-link ">
                        <span class="title">Country</span>
                    </a>
                </li>
                <li class="nav-item  ">
                    <a href="" class="nav-link ">
                        <span class="title">States</span>
                    </a>
                </li>
                <li class="nav-item  ">
                    <a href="" class="nav-link ">
                        <span class="title">Cities</span>
                    </a>
                </li>
                <li class="nav-item  ">
                    <a href="" class="nav-link ">
                        <span class="title">Postal Codes</span>
                    </a>
                </li>
                <li class="nav-item  ">
                    <a href="" class="nav-link ">
                        <span class="title">Areas</span>
                    </a>
                </li>
            </ul>
        </li>
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
        <li class="nav-item  ">
            <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-graph"></i>
                <span class="title">Products</span>
            </a>
        </li>
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