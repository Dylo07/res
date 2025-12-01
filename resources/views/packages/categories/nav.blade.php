<!-- resources/views/packages/nav.blade.php -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-0">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#packageNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="packageNavbar">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('packages.index') ? 'active' : '' }}" 
                               href="{{ route('packages.index') }}">
                                View Packages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('package-categories.*') ? 'active' : '' }}" 
                               href="{{ route('package-categories.index') }}">
                                Manage Categories
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</div>