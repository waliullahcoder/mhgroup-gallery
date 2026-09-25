<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\AdminMenuAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use App\Services\ActionButtons\ActionButtons;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class HelperClass
{
    public static function resourceDataView($model, ?string $image_column, ?array $addition_btns, string $route_path, ?string $title, string|array|null $relation_data = null, ?string $edit = null)
    {
        if (!request()->ajax()) {
            return view("admin.{$route_path}.index", compact('title'));
        }

        $type = request('type');
        if (!empty($type) && $type === 'trash') {
            $model->onlyTrashed();
        }

        $datatables = DataTables::eloquent($model)
            ->setRowId(fn($row) => 'row_' . $row->id)
            ->addIndexColumn()
            ->addColumn('checkbox', fn($row) => self::generateCheckbox($row, $relation_data))
            ->addColumn('status', fn($row) => self::generateSwitch($row, $route_path, 'edit', 'status', 'change-status'))
            ->addColumn('approve', fn($row) => self::generateSwitch($row, $route_path, 'approve', 'approved', 'approve'))
            ->addColumn('actions', fn($row) => self::generateActionButtons($row, $relation_data, $addition_btns, $edit));

        if ($image_column) {
            $datatables->addColumn($image_column, fn($row) => self::generateImageColumn($row, $image_column));
        }
         // ✅ Add categories column
        $datatables->addColumn('category_names', function($row) {
            // Safely check if relation exists
            if ($row->relationLoaded('categories') && $row->categories) {
                return $row->categories->pluck('name')->implode(', ');
            }
            return '';
        });

        return $datatables->rawColumns(['checkbox', $image_column, 'status', 'approve', 'featured', 'verified', 'actions'])->make(true);
    }

    private static function generateCheckbox($row, $relation_data): string
    {
        $count = self::hasRelations($row, $relation_data);
        if ($count === 0) {
            $class = !empty(request('type')) && request('type') === "trash" ? 'trash_multi_checkbox' : 'multi_checkbox';
            return "<div class='custom-control custom-checkbox mx-auto'>
                <input type='checkbox' class='custom-control-input {$class}' id='{$row->id}' name='multi_checkbox[]' value='{$row->id}'>
                <label for='{$row->id}' class='custom-control-label'></label>
            </div>";
        }
        return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="20" viewBox="0 0 16 20"><path d="M17,9V7A5,5,0,0,0,7,7V9a2.946,2.946,0,0,0-3,3v7a2.946,2.946,0,0,0,3,3H17a2.946,2.946,0,0,0,3-3V12A2.946,2.946,0,0,0,17,9ZM9,7a3,3,0,0,1,6,0V9H9Zm4.1,8.5-.1.1V17a1,1,0,0,1-2,0V15.6a1.487,1.487,0,1,1,2.1-.1Z" transform="translate(-4 -2)" fill="#9d9da6"></path></svg>';
    }

    private static function generateSwitch($row, string $route_path, string $action, string $field, string $class): ?string
    {
        $menuAction = AdminMenuAction::where('route', "admin.{$route_path}.{$action}")->first();
        if (!$menuAction) return null;

        $permission = Permission::find($menuAction->permission_id);
        if (!$permission || !Route::has("admin.{$route_path}.{$action}") || !Auth::user()->can($permission->name)) return null;

        $checked = $row->{$field} == 1 ? 'checked' : '';
        $url = route("admin.{$route_path}.{$action}", $row->id);

        return "<div class='form-check form-switch'><input class='form-check-input {$class} c-pointer' data-url='{$url}' type='checkbox' name='{$field}' {$checked}></div>";
    }

    private static function generateImageColumn($row, string $column): ?string
    {
        return file_exists($row[$column]) ? '<img src="' . asset($row[$column]) . '" height="30" alt=""/>' : null;
    }

    private static function generateActionButtons($row, $relation_data, $addition_btns, $edit): string
    {
        $type = request('type');
        $data = ['id' => $row->id, 'edit' => $type !== 'trash'];

        $count = self::hasRelations($row, $relation_data);
        $delete = ($count || ($row->deletable ?? 1) == 0) ? false : true;

        $edit = ($count && $edit === 'conditional') ? false : true;

        return ActionButtons::actions($data, $addition_btns, $delete, $edit);
    }

    private static function hasRelations($row, $relations): int
    {
        if (is_array($relations)) {
            foreach ($relations as $relation) {
                if ($row->{$relation}->count() > 0) return 1;
            }
        } elseif (!is_null($relations) && $row->{$relations}->count() > 0) {
            return 1;
        }
        return 0;
    }

    public static function resourceDataEdit(string $model, string $id, string $path, ?string $title = null, ?array $additionalData = null)
    {
        $data = $model::findOrFail($id);

        if (request()->ajax() && request()->has('status')) {
            $data->update(['status' => !$data->status]);
            return response()->json(['status' => 'success', 'message' => 'Status Updated!']);
        }

        return view("admin.{$path}.edit", compact('data', 'title', 'additionalData'));
    }

    public static function resourceDataDelete(string $model, string $id, array|string|null $old_image = null, array|string|null $other_images = null)
    {
        // Restore soft-deleted item
        if (request()->boolean('recovery')) {
            $data = $model::onlyTrashed()->findOrFail($id);
            $data->restore();
            return response()->json(['status' => 'success', 'message' => 'Recovered Successfully!']);
        }

        // Permanent delete (single or multiple)
        if (request()->boolean('parmanent')) {
            $ids = request()->has('id') ? request('id') : [$id];

            foreach ((array) $ids as $deleteId) {
                $data = $model::withTrashed()->findOrFail($deleteId);

                self::deleteImages($data, $old_image, $other_images);
                $data->forceDelete();
            }

            return response()->json(['status' => 'success', 'message' => 'Permanently Deleted!']);
        }

        // Soft delete multiple items
        if (request()->has('id')) {
            foreach (request('id') as $deleteId) {
                $data = $model::findOrFail($deleteId);
                self::markDeletedBy($data);
                $data->delete();
            }

            return response()->json(['status' => 'success', 'message' => 'Successfully Deleted!']);
        }

        // Soft delete single item
        $data = $model::findOrFail($id);
        self::markDeletedBy($data);
        $data->delete();
        return response()->json(['status' => 'success', 'message' => 'Successfully Deleted!']);
    }

    /**
     * Helper to delete images.
     */
    protected static function deleteImages($data, $old_image, $other_images): void
    {
        // Delete old image(s)
        foreach ((array) $old_image as $imageField) {
            $imagePath = $data->$imageField ?? null;
            if ($imagePath && file_exists($imagePath)) {
                @unlink($imagePath);
            }
        }

        // Delete other related images (like hasMany images)
        foreach ((array) $other_images as $relation) {
            $images = $data->$relation?->pluck('image') ?? [];
            foreach ($images as $imagePath) {
                if ($imagePath && file_exists($imagePath)) {
                    @unlink($imagePath);
                }
            }
        }
    }

    /**
     * Helper to track who deleted.
     */
    protected static function markDeletedBy($data): void
    {
        $table = $data->getTable();
        if (Schema::hasColumn($table, 'deleted_by')) {
            $data->update(['deleted_by' => Auth::id()]);
        }
    }

    /**
     * Store image in the storage folder and return the path.
     */
    public static function storeImage($file, $size, $storagePath, $oldImage = null)
    {
        if (!Storage::disk('public')->exists($storagePath)) {
            Storage::disk('public')->makeDirectory($storagePath, 0777, true);
        }

        // If $file is a string path instead of UploadedFile
        if (is_string($file)) {
            $originalExtension = pathinfo($file, PATHINFO_EXTENSION);
            $mime = mime_content_type($file);
        } else {
            $originalExtension = $file->getClientOriginalExtension();
            $mime = $file->getMimeType();
        }

        $fileName = Carbon::now()->toDateString() . '-' . Str::random(40);

        if (str_starts_with($mime, 'image/') && $originalExtension !== 'svg') {
            $manager = new ImageManager(new GdDriver());
            $fileName .= '.webp';
            $image = $manager->read($file);
            $image->scaleDown(height: $size);

            $filePath = $storagePath . '/' . $fileName;
            Storage::disk('public')->put($filePath, (string) $image->toWebp(100));
        } else {
            $fileName .= '.' . $originalExtension;
            $filePath = $storagePath . '/' . $fileName;

            if (is_string($file)) {
                Storage::disk('public')->put($filePath, file_get_contents($file));
            } else {
                $file->storeAs($storagePath, $fileName, 'public');
            }
        }

        // Remove old image if it exists
        $oldImage = preg_replace('/^storage\//', '', $oldImage);
        if ($oldImage && Storage::disk('public')->exists($oldImage)) {
            Storage::disk('public')->delete($oldImage);
        }

        return ['status' => 'success', 'path_name' => 'storage/' . $filePath];
    }

    /**
     * Save the image and return the file path.
     */
    public static function saveImage($image, $width, $savePath, $oldImage = null)
    {
        if ($image) {
            $response = self::storeImage($image, $width, $savePath, $oldImage);
            return $response['status'] === 'success' ? $response['path_name'] : null;
        }
        return null;
    }

    /**
     * Generate unique slug.
     */
    public static function generateUniqueSlug($model, $slugField, $value, $id = null)
    {
        $slug = Str::slug($value);
        $originalSlug = $slug;
        $i = 1;

        $query = (new $model)->newQuery(); // FIXED: instantiate from class string

        if (method_exists($model, 'bootSoftDeletes')) {
            $query = $query->withTrashed();
        }

        while ($query->where($slugField, $slug)->where(function ($q) use ($id) {
            if ($id) {
                $q->where('id', '!=', $id);
            }
        })->exists()) {
            $slug = $originalSlug . '-' . $i++;
        }

        return $slug;
    }

    /**
     * Converts number to words.
     */
    public static function convertNumber($number)
    {
        $words = [
            0 => '',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'forty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety'
        ];

        if ($number == 0) return 'zero';

        $output = '';
        if ($number >= 10000000) {
            $output .= self::convertNumber(floor($number / 10000000)) . ' crore ';
            $number %= 10000000;
        }
        if ($number >= 100000) {
            $output .= self::convertNumber(floor($number / 100000)) . ' lakh ';
            $number %= 100000;
        }
        if ($number >= 1000) {
            $output .= self::convertNumber(floor($number / 1000)) . ' thousand ';
            $number %= 1000;
        }
        if ($number >= 100) {
            $output .= self::convertNumber(floor($number / 100)) . ' hundred ';
            $number %= 100;
        }
        if ($number > 0) {
            $output .= $words[$number] ?? $words[floor($number / 10) * 10] . ' ' . $words[$number % 10];
        }

        return trim($output);
    }

    /**
     * Generates localized Bangla text.
     */
    public static function Bangla($value)
    {
        $f = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '.', '/', '-'];
        $r = ['১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯', '০', '.', '/', '-'];
        return str_replace($f, $r, $value);
    }

    public static function makeCombinations($arrays)
    {
        $result = array(array());
        foreach ($arrays as $property => $property_values) {
            $tmp = array();
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, array($property => $property_value));
                }
            }
            $result = $tmp;
        }
        return $result;
    }
}
