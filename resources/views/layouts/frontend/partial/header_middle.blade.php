<!-- CSS -->
<style>
#search-results ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

#search-results li:hover {
    background-color: #f8f9fa;
}

#search-results img {
    border-radius: 4px;
}
</style>
<style>
/* ================== General ================== */
body {
    font-family: Arial, sans-serif;
    margin:0;
    padding:0;
}

/* Hamburger */
.hamburger {
    background:none;
    border:none;
    cursor:pointer;
    display:flex;
    flex-direction:column;
    gap:4px;
    padding:5px;
}

.hamburger span {
    display:block;
    width:25px;
    height:3px;
    background:#333;
    border-radius:2px;
}

/* Overlay */
.sidebar-overlay {
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.4);
    z-index:999;
    display:none;
}

/* Sidebar */
.sidebar {
    position: fixed;
    top: 0px; /* sticky header height */
    left: -280px;
    width: 260px;
    height: calc(100vh - 60px); /* remaining screen */
    background: #fff;
    z-index: 1000;
    overflow-y: auto;
    box-shadow: 2px 0 12px rgba(0,0,0,0.2);
    transition: 0.3s;
}
.sidebar.active {
    left:0;
}

.sidebar-header {
    padding:10px;
    font-weight:bold;
    font-size:16px;
    border-bottom:1px solid #eee;
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:#ebf5f4;
}

.sidebar-header button {
    background:none;
    border:none;
    font-size:20px;
    cursor:pointer;
}

/* Menu Items */
.sidebar ul {
    list-style:none;
    margin:0;
    padding:0;
}

.sidebar li {
    border-bottom:1px solid #f0f0f0;
}

.sidebar a {
    display:block;
    padding:12px 18px;
    text-decoration:none;
    color:#333;
    position:relative;
    transition:0.2s;
}

.sidebar a:hover {
    background:#f5f5f5;
}

/* Submenu */
.sidebar .sub-menu {
    display:none;
    background:#f9f9f9;
}

.sidebar .sub-menu a {
    padding-left:35px;
}

/* Arrow */
.sidebar .has-sub > a::after {
    content:"▸";
    position:absolute;
    right:18px;
    top:0px;
    font-size: 30px;
    transition:0.3s;
    color: #86ada7;
}

.sidebar .has-sub.open > a::after {
    transform: rotate(90deg);
}

/* Scrollbar */
.sidebar::-webkit-scrollbar {
    width:6px;
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.2);
    border-radius:3px;
}
@media (min-width: 992px) {
    #sidebarToggle {
        display: none !important;
    }
}
</style>
<div class="header-middle" style="background:#000000">
    <div class="container">

        <!-- Row 1: Hamburger / Logo / Cart / Login / Desktop search -->
        <div class="d-flex align-items-center justify-content-between mb-2">

            <!-- Hamburger for mobile only -->
          <button id="sidebarToggle" class="hamburger d-block d-lg-none">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <!-- Logo -->
            <a href="{{ route('home') }}">
                <img src="{{ asset(file_exists($settings->logo) ? $settings->logo : 'frontend/images/logo/logo.jpg') }}"
                     height="70" alt="{{ $settings->app_name }}">
            </a>

            <!-- Desktop Search (desktop only) -->
            <div class="d-none d-lg-block flex-grow-1 mx-3 search-area">
                <form action="#" method="GET" class="position-relative w-100">
                    <input type="search" class="form-control w-100" name="query"
                           placeholder="Search product's name here" required id="product-search-input">
                    <button type="submit" class="btn btn-primary position-absolute end-0 top-0 h-100">
                        <i class="fas fa-search"></i>
                    </button>

                    <div id="search-results" class="bg-white border shadow-sm"
                         style="position:absolute; top:100%; left:0; right:0; display:none; z-index:2000; max-height:350px; overflow-y:auto; border-radius:5px;">
                    </div>
                </form>
            </div>

            <!-- Cart + Login -->
            <div class="d-flex align-items-center gap-2" style="margin-right:50px;">
                <a href="{{ route('cart.index') }}" class="cart-icon">
                    <i class="fa-solid fa-cart-shopping" style="color:#D4AF37"></i>
                    <span class="cart-count">{{ count(session('cart', [])) }}</span>
                </a>

                @if(Auth::check())
                    <a href="{{ Auth::user()->role_status == 0 ? route('frontend.user.dashboard') : route('admin.dashboard') }}">
                        <b style="font-size: 13px;color: #D4AF37">{{ Auth::user()->name }}</b>
                    </a>
                @else
                    <a href="{{ route('auth.signinPage') }}" style="font-size: 13px;color: #D4AF37">Sign in</a>
                @endif
            </div>
        </div>

        <!-- Row 2: Mobile Search only -->
        <div class="d-lg-none position-relative w-100 mb-2">
            <form action="#" method="GET" class="w-100 position-relative mobile-search-area">
                <input type="search" class="form-control w-100" name="query"
                       placeholder="Search product's name here" required id="mobile-product-search-input">
                <button type="submit" class="btn btn-primary position-absolute end-0 top-0 h-100">
                    <i class="fas fa-search"></i>
                </button>

                <div id="mobile-search-results" class="bg-white border shadow-sm"
                     style="position:absolute; top:100%; left:0; right:0; display:none; z-index:2000; max-height:350px; overflow-y:auto; border-radius:5px;">
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header d-flex justify-content-between align-items-center">
            <span>Menu</span>
            <button id="closeSidebar">✕</button>
        </div>

        <ul>
            <li><a href="{{ route('home') }}">Home</a></li>

            @foreach($menus['middle_menus'] as $menu)
                <li>
                    <a href="{{ route('category.index', [$menu->category_id, $menu->category_slug, $menu->name]) }}">
                        {{ $menu->name }}
                    </a>
                </li>
            @endforeach

            <!-- <div class="sidebar-header">Mega Menu</div>

            @foreach ($menus['mega_menus'] as $menu)
                <li class="{{ isset($menus['sub_menus'][$menu->id]) ? 'has-sub' : '' }}">
                    <a href="{{ route('category.index', [$menu->category_id, $menu->category_slug, $menu->name]) }}">
                        {{ $menu->name }}
                    </a>

                    @if(isset($menus['sub_menus'][$menu->id]) && count($menus['sub_menus'][$menu->id]) > 0)
                    @php
                    $items = $menus['sub_menus'][$menu->id]->take(20);
                    @endphp
                        <ul class="sub-menu">
                            @foreach($items as $item)
                                <li>
                                    <a href="{{ route('category.singleCategoryPage', $item->id) }}">
                                        → {{ $item->name }}
                                    </a>
                                </li>
                            @endforeach
                            @if($menus['sub_menus'][$menu->id]->count() > 20)
                               <li><a href="{{ route('category.index', [$menu->category_id, $menu->category_slug,$menu->name]) }}" data-menu="menu-{{ $menu->id }}" style="padding-left:30px; color:#549a95;">
                                    Read More →
                                </a>
                                </li>
                            @endif
                        </ul>
                         
                    @endif
                </li>
            @endforeach
        </ul> -->
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
</div>

   
<!-- JS -->
<script>
document.addEventListener('DOMContentLoaded', function(){

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');
    const closeBtn = document.getElementById('closeSidebar');

    toggleBtn.addEventListener('click', function(){
        sidebar.classList.add('active');
        overlay.style.display = 'block';
    });

    closeBtn.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);

    function closeSidebar(){
        sidebar.classList.remove('active');
        overlay.style.display = 'none';
    }

    // Submenu toggle
    document.querySelectorAll('.sidebar .has-sub > a').forEach(function(item){
        item.addEventListener('click', function(e){
            e.preventDefault();
            const parent = this.parentElement;
            const subMenu = this.nextElementSibling;

            // toggle display
            if(subMenu.style.display === 'block'){
                subMenu.style.display = 'none';
                parent.classList.remove('open');
            } else {
                subMenu.style.display = 'block';
                parent.classList.add('open');
            }
        });
    });
});
</script>


