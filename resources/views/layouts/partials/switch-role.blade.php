<li class="slide has-sub">

    <a href="javascript:void(0);" class="sidebar__menu-item" id="roleSwitchDropdown">
        <i class="fa-solid fa-angle-down side-menu__angle"></i>
        <div class="side-menu__icon"><i class="fa-solid fa-exchange-alt"></i></div>
        <span class="sidebar__menu-label">Switch Account</span>
    </a>

    <ul class="sidebar-menu child1">
        @foreach(auth()->user()->roles as $role)
            @php $isActive = session('active_role') === $role->name ? 'active' : ''; @endphp
            <li class="slide">
                <a href="javascript:void(0);" class="sidebar__menu-item switch-role {{ $isActive }}" data-role="{{ $role->name }}">
                    {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                    @if($isActive) <span class="badge bg-primary me-2">Active</span> @endif
                </a>
            </li>
        @endforeach
    </ul>

</li>

<form id="switchRoleForm" action="{{ route('switch.role') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="role" id="selectedRole">
</form>
