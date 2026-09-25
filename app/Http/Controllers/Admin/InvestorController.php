<?php

namespace App\Http\Controllers\Admin;

use App\Models\Coa;
use App\HelperClass;
use App\Models\User;
use App\Models\Investor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class InvestorController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'investor';
        $this->title = 'investor Setup';
        $this->create_title = 'Add investor';
        $this->edit_title = 'Update investor';
        $this->model = Investor::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addition_btns = [[
            'parameter' => true,
            'target' => '_self',
            'title' => 'View Investor',
            'route' => "admin.{$this->path}.show",
            'icon' => '<i class="fas fa-eye"></i>',
            'class' => 'btn btn-sm btn-primary mw-fit',
        ]];

        return HelperClass::resourceDataView($this->model::with(['invests'])->orderBy('id', 'desc'), null, $addition_btns, $this->path, $this->title, ['invests', 'transactions']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = $this->create_title;
        return view("admin.{$this->path}.create", compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'nullable|image',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'required|unique:users,phone',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = User::create([
                    'role_status' => 2,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'user_name' => $request->phone,
                    'image' => $request->hasFile('image') ? HelperClass::saveImage($request->image, 300, $this->path) : null,
                    'password' => Hash::make($request->phone),
                    'created_by' => Auth::id(),
                ]);

                $parent = Coa::findOrFail(11);
                $prefix = $parent->head_code;
                $maxCode = Coa::where('parent_id', $parent->id)->max('head_code');
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

                $parent = Coa::findOrFail(42);
                $prefix = $parent->head_code;
                $maxCode = Coa::where('parent_id', $parent->id)->max('head_code');
                if ($maxCode) {
                    $next = str_pad((int) substr($maxCode, strlen($prefix)) + 1, 2, '0', STR_PAD_LEFT);
                    $headCode = $prefix . $next;
                } else {
                    $headCode = $prefix . '01';
                }

                $profit_account = Coa::create([
                    'parent_id'   => $parent->id,
                    'head_code'   => $headCode,
                    'head_name'   => $request->name . ' - Profit',
                    'transaction' => true,
                    'general'     => false,
                    'head_type'   => $parent->head_type,
                    'status'      => true,
                    'updateable'  => false,
                    'created_by'  => Auth::id(),
                ]);

                $this->model::create([
                    'user_id' => $user->id,
                    'coa_id' => $account->id,
                    'profit_head' => $profit_account->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'nid' => $request->nid,
                    'bkash' => $request->bkash,
                    'rocket' => $request->rocket,
                    'nagad' => $request->nagad,
                    'bank' => $request->bank,
                    'branch' => $request->branch,
                    'account_name' => $request->account_name,
                    'account_no' => $request->account_no,
                    'image' => $request->hasFile('image') ? HelperClass::saveImage($request->image, 300, $this->path) : null,
                    'document' => $request->hasFile('document') ? HelperClass::saveImage($request->document, 2000, $this->path) : null,
                    'created_by' => Auth::id(),
                ]);

                $role = Role::findByName('Investor');
                $user->assignRole($role);
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
        $data = $this->model::findOrFail($id);
        $title = 'View Investor';
        return view("admin.{$this->path}.view", compact('title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (request()->ajax() && request()->has('status')) {
            $data = $this->model::findOrFail($id);
            $user = User::find($data->user_id);
            $user->update(['status' => !$user->status]);
            $data->update(['status' => !$data->status]);
            return response()->json(['status' => 'success']);
        }

        return HelperClass::resourceDataEdit($this->model, $id, $this->path, $this->edit_title);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $this->model::findOrFail($id);

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image'],
            'email' => ['nullable', 'email', 'unique:users,email,' . $data->user_id],
            'phone' => ['required', 'unique:users,phone,' . $data->user_id],
        ]);

        try {
            DB::transaction(function () use ($request, $data) {
                $user = User::findOrFail($data->user_id);
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'user_name' => $request->phone,
                    'image' => $request->hasFile('image') ? HelperClass::saveImage($request->image, 300, $this->path, $user->image) : $user->image,
                    'updated_by' => Auth::id(),
                ]);

                $account = Coa::find($data->coa_id);
                if ($account) {
                    $account->update([
                        'head_name' => $request->name,
                        'updateable'     => false,
                        'updated_by' => Auth::id()
                    ]);
                } else {
                    $parent = Coa::findOrFail(11);
                    $prefix = $parent->head_code;
                    $maxCode = Coa::where('parent_id', $parent->id)->max('head_code');
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

                $profit_account = Coa::find($data->profit_head);
                if ($profit_account) {
                    $profit_account->update([
                        'head_name' => $request->name . ' - Profit',
                        'updateable'     => false,
                        'updated_by' => Auth::id()
                    ]);
                } else {
                    $parent = Coa::findOrFail(42);
                    $prefix = $parent->head_code;
                    $maxCode = Coa::where('parent_id', $parent->id)->max('head_code');
                    if ($maxCode) {
                        $next = str_pad((int) substr($maxCode, strlen($prefix)) + 1, 2, '0', STR_PAD_LEFT);
                        $headCode = $prefix . $next;
                    } else {
                        $headCode = $prefix . '01';
                    }

                    $profit_account = Coa::create([
                        'parent_id'   => $parent->id,
                        'head_code'   => $headCode,
                        'head_name'   => $request->name . ' - Profit',
                        'transaction' => true,
                        'general'     => false,
                        'head_type'   => $parent->head_type,
                        'status'      => true,
                        'updateable'     => false,
                        'created_by'  => Auth::id(),
                    ]);
                }

                $data->update([
                    'user_id' => $user->id,
                    'coa_id' => $account->id,
                    'profit_head' => $profit_account->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'nid' => $request->nid,
                    'bkash' => $request->bkash,
                    'rocket' => $request->rocket,
                    'nagad' => $request->nagad,
                    'bank' => $request->bank,
                    'branch' => $request->branch,
                    'account_name' => $request->account_name,
                    'account_no' => $request->account_no,
                    'image' => $request->hasFile('image') ? HelperClass::saveImage($request->image, 300, $this->path, $data->image) : $data->image,
                    'document' => $request->hasFile('document') ? HelperClass::saveImage($request->document, 2000, $this->path, $data->document) : $data->document,
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
        // Recovery Deleted Data
        if (request()->has('recovery') && request('recovery') == 'true') {
            try {
                DB::transaction(function () use ($id) {
                    $data = $this->model::onlyTrashed()->findOrFail($id);
                    $coa = Coa::onlyTrashed()->find($data->coa_id);
                    if ($coa) {
                        $coa->restore();
                    }
                    $profitCoa = Coa::onlyTrashed()->find($data->profit_head);
                    if ($profitCoa) {
                        $profitCoa->restore();
                    }
                    $user = User::onlyTrashed()->findOrFail($data->user_id);
                    $user->restore();
                    $data->restore();
                });
                return response()->json(['status' => 'success', 'message' => 'Successfully Recovered!']);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }

        // Delete Single Item Permanent
        if (request()->has('parmanent') && request('parmanent') == 'true') {
            try {
                DB::transaction(function () use ($id) {
                    $ids = request()->has('id') ? request('id') : [$id];
                    foreach ((array) $ids as $deleteId) {
                        $data = $this->model::onlyTrashed()->findOrFail($deleteId);
                        if (file_exists($data->image)) {
                            unlink($data->image);
                        }
                        if (file_exists($data->document)) {
                            unlink($data->document);
                        }
                        $coa = Coa::onlyTrashed()->find($data->coa_id);
                        if ($coa) {
                            $coa->forceDelete();
                        }
                        $profitCoa = Coa::onlyTrashed()->find($data->profit_head);
                        if ($profitCoa) {
                            $profitCoa->forceDelete();
                        }
                        $user = User::onlyTrashed()->findOrFail($data->user_id);
                        if (file_exists($user->image)) {
                            unlink($user->image);
                        }
                        $user->forceDelete();
                        $data->forceDelete();
                    }
                });
                return response()->json(['status' => 'success', 'message' => 'Permanently Deleted!']);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }

        // Delete Single Item
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
                    $profitCoa = Coa::find($data->profit_head);
                    if ($profitCoa) {
                        $profitCoa->update(['deleted_by' => Auth::id()]);
                        $profitCoa->delete();
                    }
                    $user = User::find($data->user_id);
                    if ($user) {
                        $user->update(['deleted_by' => Auth::id()]);
                        $user->delete();
                    }
                    $data->update(['deleted_by' => Auth::id()]);
                    $data->delete();
                }
            });
            return response()->json(['status' => 'success', 'message' => 'Successfully Deleted!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
