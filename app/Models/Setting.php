<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['app_name', 'title','tax','discount','discount_type','primary_phone', 'secondary_phone', 'primary_email', 'secondary_email', 'office_time', 'address', 'description', 'banner_one', 'banner_one_link', 'banner_one_status', 'banner_two', 'banner_two_link', 'banner_two_status', 'page_heading_bg', 'meta_title', 'meta_keyword', 'meta_description', 'meta_image', 'google_map', 'favicon', 'logo', 'footer_logo', 'placeholder', 'facebook_page', 'facebook_group', 'youtube', 'twitter', 'linkedin', 'google', 'whatsapp', 'instagram', 'pinterest', 'sms_api_url', 'sms_api_key', 'sms_api_id', 'bkash_status', 'nagad_status'];
}
