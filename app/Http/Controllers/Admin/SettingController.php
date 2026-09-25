<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Setting::first();
        return view('admin.setting.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'favicon' => 'image|nullable',
            'logo' => 'image|nullable',
            'footer_logo' => 'image|nullable',
            'placeholder' => 'image|nullable',
            'meta_image' => 'image|nullable',
        ]);

        $data = Setting::firstOrNew([]);

        $data->app_name = $request->app_name;
        $data->title = $request->title;
        $data->primary_phone = $request->primary_phone;
        $data->secondary_phone = $request->secondary_phone;
        $data->primary_email = $request->primary_email;
        $data->secondary_email = $request->secondary_email;
        $data->office_time = $request->office_time;
        $data->address = $request->address;
        $data->description = $request->description;
        $data->meta_title = $request->meta_title;
        $data->meta_keyword = $request->meta_keyword;
        $data->meta_description = $request->meta_description;
        $data->google_map = $request->google_map;
        $data->facebook_page = $request->facebook_page;
        $data->facebook_group = $request->facebook_group;
        $data->youtube = $request->youtube;
        $data->twitter = $request->twitter;
        $data->linkedin = $request->linkedin;
        $data->google = $request->google;
        $data->whatsapp = $request->whatsapp;
        $data->instagram = $request->instagram;
        $data->pinterest = $request->pinterest;
        $data->banner_one_link = $request->banner_one_link;
        $data->banner_one_status = $request->banner_one_status;
        $data->banner_two_link = $request->banner_two_link;
        $data->banner_two_status = $request->banner_two_status;
        $data->sms_api_url = $request->sms_api_url;
        $data->sms_api_key = $request->sms_api_key;
        $data->sms_api_id = $request->sms_api_id;
        $data->tax = $request->tax;
        $data->discount = $request->discount;
        $data->discount_type = $request->discount_type;

        // Images
        if ($request->hasFile('meta_image')) {
            $data->meta_image = HelperClass::saveImage($request->file('meta_image'), 500, 'settings', $data->meta_image);
        }

        if ($request->hasFile('favicon')) {
            $data->favicon = HelperClass::saveImage($request->file('favicon'), 100, 'settings', $data->favicon);
        }

        if ($request->hasFile('logo')) {
            $data->logo = HelperClass::saveImage($request->file('logo'), 400, 'settings', $data->logo);
        }

        if ($request->hasFile('footer_logo')) {
            $data->footer_logo = HelperClass::saveImage($request->file('footer_logo'), 400, 'settings', $data->footer_logo);
        }

        if ($request->hasFile('placeholder')) {
            $data->placeholder = HelperClass::saveImage($request->file('placeholder'), 800, 'settings', $data->placeholder);
        }

        if ($request->hasFile('banner_one')) {
            $data->banner_one = HelperClass::saveImage($request->file('banner_one'), 1920, 'settings', $data->banner_one);
        }

        if ($request->hasFile('banner_two')) {
            $data->banner_two = HelperClass::saveImage($request->file('banner_two'), 1200, 'settings', $data->banner_two);
        }

        if ($request->hasFile('page_heading_bg')) {
            $data->page_heading_bg = HelperClass::saveImage($request->file('page_heading_bg'), 1920, 'settings', $data->page_heading_bg);
        }

        $data->save();

        $this->clearCache();

        return back()->withSuccessMessage('Updated Successfully!');
    }

    private function clearCache(): void
    {
        Artisan::call('optimize:clear');
        Artisan::call('config:cache');
    }
}
