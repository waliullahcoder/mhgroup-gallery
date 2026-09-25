<div class="navbar-header bg-white shadow-sm">
    <div class="px-sm-3 px-10px d-flex gap-2 align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <button class="brand-toggle btn btn-sm px-0 d-flex @if (Session::has('sidebar-collapse')) active @endif">
                <span class="svg-icon svg-icon svg-icon-xl">
                    <!-- Existing toggle SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24"
                        height="24" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon points="0 0 24 0 24 24 0 24"></polygon>
                            <path
                                d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z"
                                fill="#3699FF" fill-rule="nonzero"
                                transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999)">
                            </path>
                            <path
                                d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z"
                                fill="#3699FF" fill-rule="nonzero" opacity="0.3"
                                transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999)">
                            </path>
                        </g>
                    </svg>
                </span>
            </button>
        </div>

        <div class="d-flex align-items-center gap-3">
            @if(Auth::user()->hasRole('Software Admin'))
            {{-- Order Notification --}}
            <div class="dropdown">
                <button class="btn btn-light position-relative" type="button" id="orderDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="material-symbols-outlined fs-24">notifications</span>
                    @if($newOrdersCount > 0)
                        <span id="order-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $newOrdersCount }}
                        </span>
                    @endif
                </button>

                <ul class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="orderDropdown" style="min-width:300px;">
                    <li class="dropdown-header fs-6 fw-bold">নতুন অর্ডার</li>
                    @forelse($newOrders as $order)
                        <a href="{{ route('admin.orders.show', $order->id) }}"><li class="dropdown-item d-flex justify-content-between align-items-center">
                            <span>{{ $order->order_number??'N/A' }}</span>
                            <span class="badge bg-primary rounded-pill">{{ $order->status }}</span>
                        </li></a>
                    @empty
                        <li class="dropdown-item text-center text-muted">কোনো নতুন অর্ডার নেই</li>
                    @endforelse
                    @if($newOrdersCount > 0)
                        <li><hr class="dropdown-divider"></li>
                         <a href="{{ route('admin.orders.index') }}"><li class="text-center">
                            <button id="mark-read" class="btn btn-sm btn-outline-secondary">Go to Orders</button>
                        </li></a>
                    @endif
                </ul>
            </div>
            @endif

            {{-- User Profile --}}
            <div class="dropdown ms-3 header-item topbar-user">
                <button type="button" class="btn btn-light rounded-0 border-0" data-bs-toggle="dropdown">
                    <span class="d-flex align-items-center">
                        <img class="rounded-circle img-fit lazyload"
                            data-src="{{ file_exists(Auth::user()->image) ? asset(Auth::user()->image) : asset('backend/images/avatar/default/profile.jpg') }}"
                            width="50" height="50" alt="Header Avatar">
                        <span class="ms-2 text-start d-md-block d-none">
                            <span class="fs-15 d-block lh-1 ms-1">{{ Auth::user()->name }}</span>
                            <span class="ms-1 fs-12 text-muted">{{ Auth::user()->email }}</span>
                        </span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end w-100 rounded-0" style="min-width: 200px;">
                    <h6 class="fw-500 text-muted fs-12 px-3 mb-0 py-10px">Welcome {{ Auth::user()->name }}!</h6>
                    <a class="d-block fs-13 px-3 py-2 hov-bg-light" href="{{ Route('admin.profile.index') }}"><i class="fad fa-user me-1 fs-15"></i> <span>Profile</span></a>
                    <a class="d-block fs-13 px-3 py-2 hov-bg-light" href="{{ Route('admin.logout') }}"> <i class="fad fa-sign-out me-1 fs-15"></i><span>Logout</span></a>
                </div>
            </div>
        </div>
    </div>
</div>
