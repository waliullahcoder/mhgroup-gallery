<div class="header-top d-md-block d-none">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-sm" style="padding-left:20px;font-weight:bold;">Welcome to MH Gallery</div>
            <div class="top-header-links">
                @foreach ($menus['top_menus'] as $menu)
                <a target="_blank" href="{{ $menu->menu_url }}">
                    <span>{{ $menu->name }}</span> </a>
                <div class="devider"></div>
                @endforeach
            </div>
        </div>
    </div>
</div>