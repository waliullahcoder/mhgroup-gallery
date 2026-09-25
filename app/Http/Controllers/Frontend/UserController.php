<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Slider;
use App\Models\HomeSection;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sales;
use App\Models\Collection;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Coa;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\Region;
use App\Models\Area;
use App\Models\Territory;
use App\Http\Controllers\Controller;
use App\Services\FrontEndService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use App\HelperClass;
class UserController extends Controller
{
    protected $frontEndService;
    public function __construct(FrontEndService $frontEndService)
    {
        $this->frontEndService = $frontEndService;
    }

    public function signinPost(Request $request)
    {
        $request->validate([
            'phone'    => ['required','regex:/^01[3-9]\d{8}$/'],
            'password' => 'required',
        ]);

        $credentials = $request->only('phone', 'password');
        $remember = $request->has('remember'); // ✅ remember me

         if (Auth::attempt($credentials, $remember)) {

            if (auth()->user()->role_status != 0) {
                Auth::logout();
                return redirect()->back()->with('success', 'You are not allowed here');
            }

            return redirect()->route('frontend.user.dashboard')->with('success', 'Logged in successfully');
        }

        return back()
        ->withErrors(['phone' => 'Invalid mobile number or password'])
        ->withInput();
    }


        public function signupPost(Request $request)
        {
            $request->validate([
                'name'     => 'required|string|max:255',
                'phone'    => ['required','regex:/^01[3-9]\d{8}$/','unique:users,phone'],
                'password' => 'required|min:6|confirmed',
            ]);

            try {
            DB::transaction(function () use ($request) {
             // Create User section
                $user = null;
            if ($request->phone || $request->name) {
                $user = User::query()
                    ->when($request->phone, function ($q) use ($request) {
                        $q->where('phone', $request->phone);
                    })
                    ->when($request->name, function ($q) use ($request) {
                        $q->orWhere('name', $request->name);
                    })
                    ->first();

                if (!$user) {
                $user = User::create([
                'name'        => $request->name,
                'phone'        => $request->phone,
                'address'        => $request->address,
                'user_name'   => strtolower(str_replace(' ', '', $request->name)),
                'email'       => $request->phone.'@email.com',
                'password'    => Hash::make($request->password),
                'role_status' => 0,
                ]);

                Auth::login($user);
                }
                $user_id = $user->id;
                }
                // End Create User section

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

                Client::create([
                    'user_id'  => $user_id,
                    'region_id' => $request->region_id,
                    'area_id' => $request->area_id,
                    'territory_id' => $request->territory_id,
                    'coa_id' => $account->id,
                    'code' => $request->code,
                    'name' => $request->name,
                    'contact_person' => $request->contact_person,
                    'phone' => $request->phone,
                    'email' => $request->phone.'@email.com',
                    'address' => $request->address,
                    'bin_no' => $request->bin_no,
                    'credit_limit' => $request->credit_limit,
                    'created_by' => Auth::id(),
                ]);
            });
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }

          

            return redirect()
                ->route('frontend.user.dashboard')
                ->with('success', 'Welcome 🎉 Account created successfully');
        }

   
    public function forgotPasswordPost(Request $request)
    {
        // Step 1: Validate phone first
        $request->validate([
            'phone' => ['required','regex:/^01[3-9]\d{8}$/','exists:users,phone'],
        ]);

        // Find user once
        $user = User::where('phone', $request->phone)->firstOrFail();

        // Step 2: If password submitted, validate and update
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|min:6',
            ]);

            $user->update([
                'password' => Hash::make($request->password),
            ]);

             return redirect()
                ->route('auth.signinPage')->with('success', 'Password changed successfully!');
        }

        // Step 3: If no password yet, show the forgot password form with user info
        $menus = $this->frontEndService->getMenu();

        return view('frontend.auth.forgot_password', compact('user','menus'));
    }


    public function dashboard()
    {
        if (auth()->user()->role_status != 0) {
            abort(403);
        }
        $menus = $this->frontEndService->getMenu();
        
        $client = Client::where('user_id', Auth::user()->id)->first();
        $clientid = 0;
        if($client){
            $clientid=$client->id;
        }
        $sales = Sales::where('client_id', $clientid)->sum('net_amount');
        $collection = Collection::where('client_id', $clientid)->sum('amount');
        return view('frontend.user.dashboard', compact('menus','sales','collection'));
    }

    public function invoiceHistory(){
        $menus = $this->frontEndService->getMenu();
        $client = Client::where('user_id', Auth::user()->id)->first();
        $clientid = 0;
        if($client){
            $clientid=$client->id;
        }
         $sales = Sales::where('client_id', $clientid)->get();
        return view('frontend.user.invoiceList', compact('menus','sales'));
    }

    public function salesInvoice($id){
        $data = Sales::withTrashed()->findOrFail($id);
        $report_title = 'Sales Invoice';
        return view('frontend.user.salesInvoice', compact('report_title', 'data'));
        // $pdf = Pdf::loadView("frontend.user.salesInvoice", compact('report_title', 'data'));
        // $pdf->setOptions(['defaultFont' => 'solaimanlipi']);
        // return $pdf->stream('sales_voucher_' . date('d_m_Y_H_i_s') . '.pdf');
    }

    public function updateEditProfile()
    {
        $menus = $this->frontEndService->getMenu();
        return view('frontend.user.profile_edit', compact('menus'));
    }


        public function updateProfile(Request $request)
        {
            $user = Auth::user();

            $request->validate([
                'name'  => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $user->name  = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->address = $request->address;

            /* ===== IMAGE UPDATE ===== */
            if ($request->hasFile('image')) {

                // delete old image
                if ($user->image && Storage::disk('public')->exists($user->image)) {
                    Storage::disk('public')->delete($user->image);
                }

                // save new image
                $user->image = HelperClass::saveImage(
                    $request->file('image'),
                    800,
                    'users/profile',
                    $user->image
                );
            }
            $user->save();

            return back()->with('success', 'Profile updated successfully');
        }




    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('auth.signinPage')->with('success', 'You have been logged out successfully');
    }

    
}