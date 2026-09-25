@php
    if ($errors->any()) {
        foreach ($errors->all() as $error) {
            notify()->error($error, 'Error');
        }
    }
    if (session('success_message')) {
        notify()->success(session('success_message'), 'Success');
    }
@endphp
