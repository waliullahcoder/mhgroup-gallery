<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>{{ @$admin_setting->title }}</title>

<link rel="shortcut icon"
href="{{ asset(file_exists(@$admin_setting->favicon) ? $admin_setting->favicon : 'backend/images/logo/favicon.png') }}"
type="image/x-icon">

@notifyCss
@include('layouts.admin.partial.styles')
@include('layouts.admin.partial.alert')

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family: 'Poppins', sans-serif;
}

body{
height:100vh;
display:flex;
align-items:center;
justify-content:center;
background: linear-gradient(135deg,#1e3c72,#2a5298);
}

.login-container{
width:100%;
max-width:420px;
padding:20px;
}

.login-card{

background: rgba(255,255,255,0.08);
backdrop-filter: blur(12px);
border-radius:15px;
padding:40px;
box-shadow:0 15px 40px rgba(0,0,0,0.3);
color:#fff;

}

.login-card h1{
font-size:28px;
text-align:center;
margin-bottom:10px;
font-weight:700;
}

.login-card p{
text-align:center;
font-size:14px;
opacity:.7;
margin-bottom:25px;
}

.form-label{
font-size:14px;
margin-bottom:6px;
}

.form-control{

background:rgba(255,255,255,0.1);
border:1px solid rgba(255,255,255,0.2);
border-radius:8px;
color:#fff;
height:45px;

}

.form-control::placeholder{
color:#ddd;
}

.form-control:focus{

background:rgba(255,255,255,0.1);
border-color:#00c6ff;
box-shadow:none;
color:#fff;

}

.btn-login{

width:100%;
height:45px;
border:none;
border-radius:8px;
background:linear-gradient(45deg,#00c6ff,#0072ff);
color:#fff;
font-weight:600;
transition:.3s;

}

.btn-login:hover{

transform:translateY(-2px);
box-shadow:0 8px 20px rgba(0,0,0,0.3);

}

.form-check-input{

background-color:transparent;
border:1px solid #ccc;

}

.form-check-label{
font-size:14px;
}

.password-toggler{

position:absolute;
right:10px;
top:50%;
transform:translateY(-50%);
background:none;
border:none;
color:#fff;

}

.forgot-link{

font-size:13px;
color:#ddd;
text-decoration:none;

}

.forgot-link:hover{
color:#fff;
}

</style>

</head>

<body>

<div class="login-container">

<div class="login-card">

<form action="{{ route('admin.login') }}" method="POST">
@csrf

<h1>Admin Login</h1>

<p>Sign in to access the dashboard</p>

<div class="mb-3">

<label for="user_name" class="form-label">
User Name <span class="text-danger">*</span>
</label>

<input
type="text"
class="form-control"
name="user_name"
id="user_name"
value="{{ old('user_name') }}"
required>

</div>

<div class="mb-3">

<div class="d-flex justify-content-between">

<label class="form-label">
Password <span class="text-danger">*</span>
</label>

<a href="#" class="forgot-link">
Forgot password?
</a>

</div>

<div class="position-relative">

<input
type="password"
class="form-control"
id="toggle_password"
name="password"
value="{{ old('password') }}"
required>

<button type="button" class="password-toggler">
<i class="fas fa-eye-slash"></i>
</button>

</div>

</div>

<div class="mb-3">

<div class="form-check">

<input
class="form-check-input"
type="checkbox"
name="remember"
id="remember"
{{ old('remember') ? 'checked' : '' }}>

<label class="form-check-label" for="remember">
Remember me
</label>

</div>

</div>

<button type="submit" class="btn-login">
Sign In
</button>

</form>

</div>

</div>

@include('layouts.admin.partial.scripts')

</body>

</html>