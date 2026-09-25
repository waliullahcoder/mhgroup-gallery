@php
    $parentId = 'accordionItem' . $menu->id;
    $collapseId = 'collapse' . $menu->id;
    $accordionGroupId = 'accordionGroup' . $menu->id;
@endphp

<div class="accordion-item" id="{{ $parentId }}">
    <div class="bg-success d-flex align-items-center">
        <div class="flex-shrink-0 ms-2 custom-control custom-checkbox d-flex gap-2">
            <input type="checkbox" class="custom-control-input check_group" name="permission_id[]"
                id="menu{{ $menu->id }}" value="{{ $menu->permission_id }}"
                {{ in_array($menu->permission_id, $permission_id) ? 'checked' : '' }}>
            <label class="form-label custom-control-label c-pointer mb-0" for="menu{{ $menu->id }}"></label>
        </div>
        <div class="accordion-header flex-grow-1" id="heading{{ $menu->id }}">
            <button class="accordion-button collapsed text-white bg-success" type="button" data-bs-toggle="collapse"
                data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                {{ $menu->name }}
            </button>
        </div>
    </div>

    <div id="{{ $collapseId }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $menu->id }}"
        data-bs-parent="#{{ $parentAccordionId ?? 'roleAccordionParentGroup' }}">

        <div class="accordion-body">
            @if ($menu->children->isNotEmpty())
                <div class="accordion" id="{{ $accordionGroupId }}">
                    @foreach ($menu->children as $child)
                        @can($child->permission->name)
                            @include('admin.role.partials.menu_item', [
                                'menu' => $child,
                                'permission_id' => $permission_id,
                                'parentAccordionId' => $accordionGroupId, // Important!
                            ])
                        @endcan
                    @endforeach
                </div>
            @elseif ($menu->actions->isNotEmpty())
                <div class="d-flex gap-3 flex-wrap">
                    @foreach ($menu->actions as $action)
                        @can($action->permission->name)
                            <div class="w-fit">
                                <div class="custom-control custom-checkbox d-flex gap-2">
                                    <input class="custom-control-input" type="checkbox" name="permission_id[]"
                                        id="permission{{ $action->id }}" value="{{ $action->permission_id }}"
                                        {{ in_array($action->permission_id, $permission_id) ? 'checked' : '' }}>
                                    <label for="permission{{ $action->id }}" class="custom-control-label c-pointer">
                                        <span class="ms-2">{{ $action->name }}</span>
                                    </label>
                                </div>
                            </div>
                        @endcan
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
