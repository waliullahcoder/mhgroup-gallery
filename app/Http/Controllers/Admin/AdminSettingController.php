<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Models\AdminSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class AdminSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = AdminSetting::first();
        return view('admin.admin-setting.edit', compact('data'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'logo' => 'nullable|image',
            'favicon' => 'nullable|image',
            'title' => 'required|string|max:255',
            'footer_text' => 'required|string|max:255',
        ]);

        $data = AdminSetting::firstOrNew();
        $data->fill([
            'title' => $request->title,
            'invest_value' => $request->invest_value,
            'footer_text' => $request->footer_text,
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'facebook' => $request->facebook,
            'twitter' => $request->twitter,
            'linkedin' => $request->linkedin,
            'whatsapp' => $request->whatsapp,
            'google' => $request->google,
        ]);
        if ($request->hasFile('logo')) {
            $data->logo = HelperClass::saveImage($request->logo, 300, 'admin-setting/', @$data->logo);
        }

        if ($request->hasFile('small_logo')) {
            $data->small_logo = HelperClass::saveImage($request->small_logo, 300, 'admin-setting/', @$data->small_logo);
        }

        if ($request->hasFile('favicon')) {
            $data->favicon = HelperClass::saveImage($request->favicon, 150, 'admin-setting/', @$data->favicon);
        }
        $data->save();
        $this->clearCache();
        return back()->withSuccessMessage('Updated successfully!');
    }

    private function clearCache(): void
    {
        Artisan::call('optimize:clear');
        Artisan::call('config:cache');
    }
}
