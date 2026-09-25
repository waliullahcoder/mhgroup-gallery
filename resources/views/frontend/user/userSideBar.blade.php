 <style>
    [type=button]:not(:disabled), [type=reset]:not(:disabled), [type=submit]:not(:disabled), button:not(:disabled){
        border:none !important;
        padding-left:30px !important;
    }
 </style>
 <div class="col-lg-3">
           <div class="card shadow-sm">
                <div class="card-body p-0">

                    <ul class="list-group list-group-flush profile-menu">
                        <li class="list-group-item">
                            <a href="{{ route('frontend.user.dashboard') }}" class="d-flex align-items-center gap-2">
                                <i class="fa fa-tachometer-alt"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('user.profile.edit') }}" class="d-flex align-items-center gap-2">
                                <i class="fa fa-user-edit"></i>
                                Profile Update
                            </a>
                        </li>

                        <li class="list-group-item">
                            <a href="{{ route('orders.index') }}" class="d-flex align-items-center gap-2">
                                <i class="fa fa-box"></i>
                                My Orders
                            </a>
                        </li>

                        <li class="list-group-item">
                            <a href="{{ route('wishlist.index') }}" class="d-flex align-items-center gap-2">
                                <i class="fa fa-heart text-danger"></i>
                                Wishlist
                            </a>
                        </li>

                         <li class="list-group-item">
                            <a href="{{route('frontend.user.invoiceHistory')}}" class="d-flex align-items-center gap-2">
                                <i class="fa fa-credit-card"></i>
                                Invoice History
                            </a>
                        </li>




                        <li class="list-group-item p-0">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="d-flex align-items-center gap-2 px-3 py-2 text-danger w-100">
                                    <i class="fa fa-sign-out-alt"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </li>



                    </ul>

                </div>
            </div>

        </div>