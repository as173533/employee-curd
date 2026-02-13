<div> {{-- ✅ SINGLE ROOT --}}

    <h3 class="fw-bold mb-4">
        Dashboard
    </h3>

    {{-- WELCOME --}}
    <div class="alert alert-success">
        Welcome back, <b>{{ auth()->user()->name }} {{ session()->get('password_hash_web') }}</b> 👋
    </div>


    {{-- STATISTICS --}}
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="text-muted">Users</div>
                    <h4 class="fw-bold">120</h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="text-muted">Orders</div>
                    <h4 class="fw-bold">87</h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="text-muted">Revenue</div>
                    <h4 class="fw-bold">$5,200</h4>
                </div>
            </div>
        </div>

    </div>


    {{-- RECENT ACTIVITY --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Recent Activity</h5>

            <ul class="list-group list-group-flush">
                <li class="list-group-item">User John created an order</li>
                <li class="list-group-item">Payment received from Alice</li>
                <li class="list-group-item">New customer registered</li>
            </ul>
        </div>
    </div>

</div>
