<div class="nav-wrapper">
    <div class="navbar">

        @foreach ($menus['mega_menus'] as $menu)
            <a class="{{ isset($menus['sub_menus'][$menu->id]) ? 'mega-trigger' : '' }} {{ request()->routeIs('category.index') && request()->route('menu') == $menu->name ? 'clicked' : '' }}" href="{{ route('category.index', [$menu->category_id, $menu->category_slug,$menu->name]) }}" data-menu="menu-{{ $menu->id }}">
                {{ $menu->name }}
                @if(isset($menus['sub_menus'][$menu->id]))
                    <span class="caret">▾</span>
                @endif
            </a>
        @endforeach

    </div>
    <!-- GLOBAL MEGA MENU -->
    <div class="mega-wrapper">
        @foreach ($menus['mega_menus'] as $menu)
            @if(isset($menus['sub_menus'][$menu->id]))
                @php
                    $items = $menus['sub_menus'][$menu->id]->take(45);
                @endphp
                <div class="mega-menu" id="menu-{{ $menu->id }}">
                    <div class="mega-grid">

                        @foreach ($items->chunk(9) as $chunk)
                            <div class="mega-column">

                                @foreach ($chunk as $item)
                                    <a href="{{ route('category.singleCategoryPage', $item->id) }}">
                                        {{ $item->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    @if($menus['sub_menus'][$menu->id]->count() > 45)
                                <a href="{{ route('category.index', [$menu->category_id, $menu->category_slug,$menu->name]) }}" data-menu="menu-{{ $menu->id }}" style="padding-left:30px; color:#549a95;">
                                    Read More →
                                </a>
                        @endif
                </div>
                        
            @endif

        @endforeach

     </div>
</div>


<script>
const triggers = document.querySelectorAll('.mega-trigger');
const wrapper = document.querySelector('.mega-wrapper');
const menus = document.querySelectorAll('.mega-menu');
const navWrapper = document.querySelector('.nav-wrapper');

triggers.forEach(trigger => {
    trigger.addEventListener('mouseenter', () => {
        if (window.innerWidth <= 600) return;

        wrapper.style.display = 'block';
        menus.forEach(menu => menu.classList.remove('active'));

        const target = document.getElementById(trigger.dataset.menu);
        if (target) target.classList.add('active');
    });

    // Mobile click
    trigger.addEventListener('click', e => {
        if (window.innerWidth > 600) return;
        e.preventDefault();

        wrapper.style.display =
            wrapper.style.display === 'block' ? 'none' : 'block';

        menus.forEach(menu => menu.classList.remove('active'));
        const target = document.getElementById(trigger.dataset.menu);
        if (target) target.classList.add('active');
    });
});

navWrapper.addEventListener('mouseleave', () => {
    if (window.innerWidth > 600) {
        wrapper.style.display = 'none';
    }
});
</script>


