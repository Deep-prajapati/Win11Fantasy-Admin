<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="index.html" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{asset('assets/Logo.svg')}}" alt="logo.." srcset="">
            </span>
            <!-- <span class="app-brand-text demo menu-text fw-bolder ms-2">{{ env('APP_NAME') }}</span> -->
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{menuActive('admin.dashboard')}}">
            <a href="{{route('admin.dashboard')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <!-- Layouts -->
        <li class="menu-item {{menuActive('admin.users.*',5)}}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Layouts">User Management</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item {{menuActive('admin.users.bots.add',5)}}">
                    <a href="{{route('admin.users.bots.add')}}" class="menu-link">
                        <div data-i18n="Without menu">Add Bots</div>
                    </a>
                </li>
                <li class="menu-item {{menuActive('admin.users.bots.list',5)}}">
                    <a href="{{route('admin.users.bots.list')}}" class="menu-link">
                        <div data-i18n="Without menu">Bot Users</div>
                    </a>
                </li>
                <li class="menu-item {{menuActive('admin.users.list',5)}}">
                    <a href="{{route('admin.users.list')}}" class="menu-link">
                        <div data-i18n="Without menu">All Users</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{menuActive('admin.recharge.*')}}">
            <a href="{{route('admin.recharge.list')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Recharges</div>
            </a>
        </li>
        <li class="menu-item {{menuActive('admin.withdawal.*')}}">
            <a href="{{route('admin.withdawal.list')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Withdawals</div>
            </a>
        </li>
        <li class="menu-item {{menuActive('admin.tnxlist')}}">
            <a href="{{route('admin.tnxlist')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Tnx List</div>
            </a>
        </li>
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Cricket</span></li>

        <li class="menu-item {{menuActive('admin.cricket.leagues')}}">
            <a href="{{route('admin.cricket.leagues')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Leagues </div>
            </a>
        </li>
        <li class="menu-item {{menuActive('admin.cricket.matches')}}">
            <a href="{{route('admin.cricket.matches')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Matches List</div>
            </a>
        </li>
        <li class="menu-item {{menuActive('admin.cricket.contest.type.*',5)}}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Layouts">Contests Types</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item {{menuActive('admin.cricket.contest.type.add',5)}}">
                    <a href="{{route('admin.cricket.contest.type.add')}}" class="menu-link">
                        <div data-i18n="Without navbar">Add</div>
                    </a>
                </li>
                <li class="menu-item {{menuActive('admin.cricket.contest.type.index',5)}}">
                    <a href="{{route('admin.cricket.contest.type.index')}}" class="menu-link">
                        <div data-i18n="Without menu">List</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item {{menuActive('admin.cricket.default.contest.*',5)}}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Layouts">Default Contests</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{menuActive('admin.cricket.default.contest.add',5)}}">
                    <a href="{{route('admin.cricket.default.contest.add')}}" class="menu-link">
                        <div data-i18n="Without navbar">Add</div>
                    </a>
                </li>
                <li class="menu-item {{menuActive('admin.cricket.default.contest.index',5)}}">
                    <a href="{{route('admin.cricket.default.contest.index')}}" class="menu-link">
                        <div data-i18n="Without menu">List</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Football</span></li>
        <li class="menu-item {{menuActive('admin.football.leagues')}}">
            <a href="{{route('admin.football.leagues')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Leagues </div>
            </a>
        </li>
        <li class="menu-item {{menuActive('admin.football.matches')}}">
            <a href="{{route('admin.football.matches')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Matches List</div>
            </a>
        </li>
        <li class="menu-item {{menuActive('admin.football.contest.type.*',5)}}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Layouts">Contests Types</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item {{menuActive('admin.football.contest.type.add',5)}}">
                    <a href="{{route('admin.football.contest.type.add')}}" class="menu-link">
                        <div data-i18n="Without navbar">Add</div>
                    </a>
                </li>
                <li class="menu-item {{menuActive('admin.football.contest.type.index',5)}}">
                    <a href="{{route('admin.football.contest.type.index')}}" class="menu-link">
                        <div data-i18n="Without menu">List</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item {{menuActive('admin.football.default.contest.*',5)}}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Layouts">Default Contests</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{menuActive('admin.football.default.contest.add',5)}}">
                    <a href="{{route('admin.football.default.contest.add')}}" class="menu-link">
                        <div data-i18n="Without navbar">Add</div>
                    </a>
                </li>
                <li class="menu-item {{menuActive('admin.football.default.contest.index',5)}}">
                    <a href="{{route('admin.football.default.contest.index')}}" class="menu-link">
                        <div data-i18n="Without menu">List</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Others</span></li>
        <li class="menu-item {{menuActive('admin.settings')}}">
            <a href="{{route('admin.settings')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Settings</div>
            </a>
        </li>
    </ul>
</aside>
