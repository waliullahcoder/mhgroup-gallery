<?php

namespace App\Services\ActionButtons;

use App\Models\AdminMenu;
use App\Models\AdminMenuAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Spatie\Permission\Models\Permission;

class ActionButtons
{
    public static function actions($data, $additionalButtons = null, $deleteCheck = true, $permitEdit = true)
    {
        $actions = "<div class='btn-group'>";
        $routeName = Request::route()->getName();
        $menu = AdminMenu::where('route', $routeName)->first() ?? AdminMenuAction::where('route', $routeName)->first();

        $editRoute = str_replace('index', 'edit', $menu->route ?? '');
        $deleteRoute = str_replace('index', 'destroy', $menu->route ?? '');

        $actions .= self::getEditOrRecoverButton($editRoute, $deleteRoute, $data, $permitEdit);
        $actions .= self::getAdditionalButtons($additionalButtons, $data);
        $actions .= self::getDeleteButton($deleteRoute, $data, $deleteCheck);

        return $actions . '</div>';
    }

    protected static function getEditOrRecoverButton($editRoute, $deleteRoute, $data, $permitEdit)
    {
        $button = '';
        $menuAction = AdminMenuAction::where('route', $editRoute)->first();
        if ($menuAction) {
            $permission = Permission::find($menuAction->permission_id);
            if ($permission && Auth::user()->can($permission->name)) {
                if (isset($data['edit']) && $data['edit'] && $permitEdit) {
                    $button = '<a href="' . route($editRoute, $data['id']) . '" class="btn btn-sm btn-warning border-0 px-10px tt link-edit" title="Edit"><i class="far fa-pencil-alt"></i></a>';
                } elseif (isset($data['edit']) && !$data['edit']) {
                    $button = '<button type="button" class="btn btn-sm btn-success border-0 px-10px tt link-recovery" data-url="' . route($deleteRoute, $data['id']) . '" title="Recover"><span class="material-symbols-outlined">recycling</span></button>';
                }
            }
        }
        return $button;
    }

    protected static function getAdditionalButtons($buttons, $data)
    {
        $html = '';

        if (is_array($buttons)) {
            foreach ($buttons as $btn) {
                $permission = Permission::where('name', $btn['route'])->first();
                if ($permission && Auth::user()->can($permission->name)) {
                    $url = $btn['parameter'] ? route($btn['route'], $data['id']) : route($btn['route']);
                    $html .= '<a href="' . $url . '" class="' . ($btn['class'] ?? '') . ' tt" target="' . ($btn['target'] ?? '') . '" title="' . ($btn['title'] ?? '') . '">' . ($btn['icon'] ?? '') . ($btn['text'] ?? '') . '</a>';
                }
            }
        } elseif (is_array($buttons) && isset($buttons['route'])) {
            $permission = Permission::where('name', $buttons['route'])->first();
            if ($permission && Auth::user()->can($permission->name)) {
                $url = $buttons['parameter'] ? route($buttons['route'], $data['id']) : route($buttons['route']);
                $html .= '<a href="' . $url . '" class="' . ($buttons['class'] ?? '') . ' tt" target="' . ($buttons['target'] ?? '') . '" title="' . ($buttons['title'] ?? '') . '">' . ($buttons['icon'] ?? '') . ($buttons['text'] ?? '') . '</a>';
            }
        } elseif (!is_null($buttons)) {
            $html .= $buttons;
        }

        return $html;
    }

    protected static function getDeleteButton($route, $data, $deleteCheck)
    {
        $button = '';
        if (!$deleteCheck) return $button;

        $menuAction = AdminMenuAction::where('route', $route)->first();
        if ($menuAction) {
            $permission = Permission::find($menuAction->permission_id);
            if ($permission && Auth::user()->can($permission->name)) {
                $title = $data['edit'] ? 'Delete' : 'Delete Permanently';
                $class = $data['edit'] ? 'link-delete' : 'trash_delete';
                $button = '<button type="button" class="btn btn-sm btn-danger border-0 px-10px tt ' . $class . '" data-url="' . route($route, $data['id']) . '" title="' . $title . '"><span class="material-symbols-outlined">delete_forever</span></button>';
            }
        }
        return $button;
    }
}
