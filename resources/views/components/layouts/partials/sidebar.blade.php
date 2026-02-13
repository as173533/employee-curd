<div class="bg-white border-end sidebar p-3 d-flex flex-column" style="width:250px;">

    <h5 class="mb-4">Menu</h5>

    <ul class="nav nav-pills flex-column gap-2 flex-grow-1">

        <li>
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : 'text-dark' }}">
                <i class="fa fa-home me-2"></i> Dashboard
            </a>
        </li>

        <li>
            <a href="{{ route('users') }}" 
                class="nav-link {{ request()->routeIs('users') ? 'active' : 'text-dark' }}">
                <i class="fa fa-users me-2"></i> Users
            </a>
        </li>

        <li>
            <a href="#" class="nav-link text-dark">
                <i class="fa fa-cog me-2"></i> Settings
            </a>
        </li>
        
        

    </ul>
    <form method="POST" action="{{ route('logout') }}">
        @csrf

        <button type="submit" class="nav-link text-danger w-100 text-start border-0 bg-transparent">
            <i class="fa fa-sign-out-alt me-2"></i>
            Logout
        </button>
    </form>
    

</div>
