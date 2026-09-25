<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'user';
        $this->title = 'Admin Setup';
        $this->create_title = 'Add Admin';
        $this->edit_title = 'Update Admin';
        $this->model = User::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addition_btns = [[
            'parameter' => true,
            'target' => '_self',
            'title' => 'Change Password',
            'route' => 'admin.user.password',
            'icon' => '<i class="fal fa-key"></i>',
            'class' => 'btn btn-sm btn-primary mw-fit border-0 fs-15',
        ]];

        return HelperClass::resourceDataView($this->model::with('roles')->whereNotIn('id', [Auth::user()->id])->whereNotIn('user_name', ['admin'])->orderBy('id', 'desc'), null, $addition_btns, $this->path, $this->title);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = $this->create_title;
        $roles = Role::whereNotIn('name', ['Software Admin', 'Investor'])->orderBy('name', 'asc')->get();
        return view("admin.{$this->path}.create", compact('title', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'user_name'  => ['required', 'string', 'unique:users,user_name'],
            'email'      => ['nullable', 'email', 'unique:users,email'],
            'phone'      => ['nullable', 'unique:users,phone'],
            'password'   => ['required', Password::min(8), 'confirmed'],
            // 'password'   => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols(), 'confirmed'],
            'role_id'    => ['required', 'exists:roles,id'],
            'image'      => ['nullable', 'image'],
        ]);

        $userData = [
            'name'        => $validated['name'],
            'user_name'   => $validated['user_name'],
            'email'       => $validated['email'],
            'phone'       => $validated['phone'],
            'password'    => Hash::make($validated['password']),
            'image'       => isset($validated['image']) ? HelperClass::saveImage($validated['image'], 300, $this->path) : null,
            'created_by'  => Auth::id(),
        ];

        $user = $this->model::create($userData);

        $user->assignRole(Role::findById($validated['role_id']));

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
    public function edit(string $id)
    {
        $additionalData = ['roles' => Role::whereNotIn('name', ['Software Admin', 'Investor'])->orderBy('name', 'asc')->get()];
        return HelperClass::resourceDataEdit($this->model, $id, $this->path, $this->edit_title, $additionalData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user_name' => ['required', 'string', Rule::unique('users', 'user_name')->ignore($id)],
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($id)],
            'phone' => ['nullable', Rule::unique('users', 'phone')->ignore($id)],
            'role_id' => ['required', 'exists:roles,id'],
            'image'      => ['nullable', 'image'],
        ]);

        $data = $this->model::findOrFail($id);
        $data->update([
            'name'       => $request->name,
            'user_name'  => $request->user_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'image'       => isset($validated['image']) ? HelperClass::saveImage($validated['image'], 300, $this->path, $data->image) : $data->image,
            'updated_by' => Auth::id(),
        ]);

        $role = Role::findById($request->role_id);
        $data->syncRoles($role);

        return redirect()->route("admin.{$this->path}.index")->withSuccessMessage('Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return HelperClass::resourceDataDelete($this->model, $id, ['image', 'cover_image']);
    }

    public function changePassword(string $id)
    {
        $data = $this->model::findOrFail($id);
        return view('admin.user.password', compact('data'));
    }

    public function passwordUpdate(Request $request, string $id)
    {
        $request->validate([
            'password'   => ['required', Password::min(8), 'confirmed']
            // 'password'   => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols(), 'confirmed']
        ]);

        $user = $this->model::findOrFail($id);
        $user->password = Hash::make($request->password);
        $user->save();
        return redirect()->route("admin.{$this->path}.index")->withSuccessMessage('Password Updated Successfully!');
    }
}
