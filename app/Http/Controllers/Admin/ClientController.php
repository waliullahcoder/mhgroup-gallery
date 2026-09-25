<?php

namespace App\Http\Controllers\Admin;

use App\Models\Coa;
use App\HelperClass;
use App\Models\Area;
use App\Models\Client;
use App\Models\Region;
use App\Models\User;
use App\Models\Territory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClientController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'client';
        $this->title = 'Client Setup';
        $this->create_title = 'Add Client';
        $this->edit_title = 'Update Client';
        $this->model = Client::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return HelperClass::resourceDataView($this->model::with(['region', 'area', 'territory'])->orderBy('id', 'desc'), NULL, NULL, $this->path, $this->title, ['sales', 'collections', 'transactions']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->ajax() && $request->has('region_id')) {
            $areas = Area::where('region_id', $request->region_id)->where('status', true)->orderBy('name', 'asc')->get();
            return response()->json(['status' => 'success', 'areas' => $areas]);
        }

        if ($request->ajax() && $request->has('area_id')) {
            $territories = Territory::where('area_id', $request->area_id)->where('status', true)->orderBy('name', 'asc')->get();
            return response()->json(['status' => 'success', 'territories' => $territories]);
        }

        $title = $this->create_title;
        $regions = Region::where('status', true)->orderBy('name', 'asc')->get();
        return view("admin.{$this->path}.create", compact('title', 'regions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'region_id' => 'required',
            'area_id' => 'required',
            'territory_id' => 'required',
            'name' => 'required',
            'code' => 'nullable|unique:clients,code'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $parent = Coa::findOrFail(7);
                $prefix = $parent->head_code;
                $maxCode = Coa::withTrashed()->where('parent_id', $parent->id)->max('head_code');
                if ($maxCode) {
                    $next = str_pad((int) substr($maxCode, strlen($prefix)) + 1, 2, '0', STR_PAD_LEFT);
                    $headCode = $prefix . $next;
                } else {
                    $headCode = $prefix . '01';
                }
                $account = Coa::create([
                    'parent_id'   => $parent->id,
                    'head_code'   => $headCode,
                    'head_name'   => $request->name,
                    'transaction' => true,
                    'general'     => false,
                    'head_type'   => $parent->head_type,
                    'status'      => true,
                    'updateable'     => false,
                    'created_by'  => Auth::id(),
                ]);

             // Create User section
                $user = null;
            if ($request->phone || $request->email || $request->name) {
                $user = User::query()
                    ->when($request->phone, function ($q) use ($request) {
                        $q->where('phone', $request->phone);
                    })
                    ->when($request->email, function ($q) use ($request) {
                        $q->orWhere('email', $request->email);
                    })
                    ->when($request->name, function ($q) use ($request) {
                        $q->orWhere('name', $request->name);
                    })
                    ->first();

                if (!$user) {
                    $user = User::create([
                        'name'       => $request->name,
                        'email'      => $request->email,
                        'phone'      => $request->phone,
                        'user_name'  => $request->phone ?? $request->email ?? $request->name,
                        'password'   => Hash::make($request->phone ?? '12345678'),
                        'created_by' => Auth::id(),
                    ]);
                }
                $user_id = $user->id;
                }
                // End Create User section

                $this->model::create([
                    'user_id'  => $user_id,
                    'region_id' => $request->region_id,
                    'area_id' => $request->area_id,
                    'territory_id' => $request->territory_id,
                    'coa_id' => $account->id,
                    'code' => $request->code,
                    'name' => $request->name,
                    'contact_person' => $request->contact_person,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'address' => $request->address,
                    'bin_no' => $request->bin_no,
                    'credit_limit' => $request->credit_limit,
                    'created_by' => Auth::id(),
                ]);
            });
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
        return redirect()->route("admin.{$this->path}.index")->withSuccessMessage('Created Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        if ($request->ajax() && $request->has('region_id')) {
            $areas = Area::where('region_id', $request->region_id)->where('status', true)->orderBy('name', 'asc')->get();
            return response()->json(['status' => 'success', 'areas' => $areas]);
        }

        if ($request->ajax() && $request->has('area_id')) {
            $territories = Territory::where('area_id', $request->area_id)->where('status', true)->orderBy('name', 'asc')->get();
            return response()->json(['status' => 'success', 'territories' => $territories]);
        }

        $data = $this->model::findOrFail($id);
        $additionalData = [
            'regions' => Region::where('status', true)->orWhere('id', $data->region_id)->orderBy('name', 'asc')->get()
        ];
        return HelperClass::resourceDataEdit($this->model, $id, $this->path, $this->edit_title, $additionalData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'region_id' => 'required',
            'area_id' => 'required',
            'territory_id' => 'required',
            'name' => 'required',
            'code' => 'nullable|unique:clients,code,' . $id
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $data = $this->model::findOrFail($id);

                $account = Coa::find($data->coa_id);
                if ($account) {
                    $account->update([
                        'head_name' => $request->name,
                        'updateable'     => false,
                        'updated_by' => Auth::id()
                    ]);
                } else {
                    $parent = Coa::findOrFail(7);
                    $prefix = $parent->head_code;
                    $maxCode = Coa::withTrashed()->where('parent_id', $parent->id)->max('head_code');
                    if ($maxCode) {
                        $next = str_pad((int) substr($maxCode, strlen($prefix)) + 1, 2, '0', STR_PAD_LEFT);
                        $headCode = $prefix . $next;
                    } else {
                        $headCode = $prefix . '01';
                    }
                    $account = Coa::create([
                        'parent_id'   => $parent->id,
                        'head_code'   => $headCode,
                        'head_name'   => $request->name,
                        'transaction' => true,
                        'general'     => false,
                        'head_type'   => $parent->head_type,
                        'status'      => true,
                        'updateable'     => false,
                        'created_by'  => Auth::id(),
                    ]);
                }

                // Create User section
                $user = null;
            if ($request->phone || $request->email || $request->name) {
                $user = User::query()
                    ->when($request->phone, function ($q) use ($request) {
                        $q->where('phone', $request->phone);
                    })
                    ->when($request->email, function ($q) use ($request) {
                        $q->orWhere('email', $request->email);
                    })
                    ->when($request->name, function ($q) use ($request) {
                        $q->orWhere('name', $request->name);
                    })
                    ->first();

                if (!$user) {
                    $user = User::create([
                        'name'       => $request->name,
                        'email'      => $request->email,
                        'phone'      => $request->phone,
                        'user_name'  => $request->phone ?? $request->email ?? $request->name,
                        'password'   => Hash::make($request->phone ?? '12345678'),
                        'created_by' => Auth::id(),
                    ]);
                }
                $user_id = $user->id;
                }
                // End Create User section

                $data->update([
                    'user_id' => $user_id,
                    'region_id' => $request->region_id,
                    'area_id' => $request->area_id,
                    'territory_id' => $request->territory_id,
                    'coa_id' => $account->id,
                    'code' => $request->code,
                    'name' => $request->name,
                    'contact_person' => $request->contact_person,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'address' => $request->address,
                    'bin_no' => $request->bin_no,
                    'credit_limit' => $request->credit_limit,
                    'updated_by' => Auth::id(),
                ]);
            });
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }

        return redirect()->route("admin.{$this->path}.index")->withSuccessMessage('Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Restore soft-deleted item
        if (request()->boolean('recovery')) {
            try {
                DB::transaction(function () use ($id) {
                    $data = $this->model::onlyTrashed()->findOrFail($id);
                    $coa = Coa::onlyTrashed()->find($data->coa_id);
                    if ($coa) {
                        $coa->restore();
                    }
                    $data->restore();
                });

                return response()->json(['status' => 'success', 'message' => 'Successfully Recovered!']);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }

        // Permanent delete (single or multiple)
        if (request()->boolean('parmanent')) {
            try {
                DB::transaction(function () use ($id) {
                    $ids = request()->has('id') ? request('id') : [$id];
                    foreach ((array) $ids as $deleteId) {
                        $data = $this->model::onlyTrashed()->findOrFail($deleteId);
                        $coa = Coa::onlyTrashed()->find($data->coa_id);
                        if ($coa) {
                            $coa->forceDelete();
                        }
                        $data->forceDelete();
                    }
                });

                return response()->json(['status' => 'success', 'message' => 'Permanently Deleted!']);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }

        // Soft delete multiple items
        try {
            DB::transaction(function () use ($id) {
                $ids = request()->has('id') ? request('id') : [$id];
                foreach ((array) $ids as $deleteId) {
                    $data = $this->model::findOrFail($deleteId);

                    $coa = Coa::find($data->coa_id);
                    if ($coa) {
                        $coa->update(['deleted_by' => Auth::id()]);
                        $coa->delete();
                    }

                    $table = $data->getTable();
                    if (Schema::hasColumn($table, 'deleted_by')) {
                        $data->update(['deleted_by' => Auth::id()]);
                    }
                    $data->delete();
                }
            });

            return response()->json(['status' => 'success', 'message' => 'Successfully Deleted!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
