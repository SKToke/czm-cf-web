<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>{{config('admin.title')}} | {{ __('admin.login') }}</title>
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

		@if(!is_null($favicon = Admin::favicon()))
		<link rel="shortcut icon" href="{{$favicon}}">
		@endif

		<link rel="stylesheet" href="{{ Admin::asset("open-admin/css/styles.css")}}">
		<script src="{{ Admin::asset("bootstrap5/bootstrap.bundle.min.js")}}"></script>

        <script>
            function togglePassword() {
                var passwordField = document.getElementById("password");
                var toggleIcon = document.getElementById("togglePassword")
                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    toggleIcon.classList.remove('icon-eye');
                    toggleIcon.classList.add('icon-eye-slash');
                } else {
                    passwordField.type = "password";
                    toggleIcon.classList.remove('icon-eye-slash');
                    toggleIcon.classList.add('icon-eye');
                }
            }
        </script>

	</head>
	<body class="bg-light" @if(config('admin.login_background_image'))style="background: url({{config('admin.login_background_image')}}) no-repeat;background-size: cover;"@endif>
		<div class="d-flex justify-content-center align-items-center h-100">
			<div class="container m-4" style="max-width:400px;">
				<h1 class="text-center mb-3 h2"><a class="text-decoration-none text-dark" href="{{ admin_url('/') }}">{{config('admin.name')}}</a></h1>
				<div class="bg-body p-4 shadow-sm rounded-3">

					@if($errors->has('attempts'))
						<div class="alert alert-danger m-0 text-center">{{$errors->first('attempts')}}</div>
					@else

					<form action="{{ admin_url('auth/login') }}" method="post">

						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<div class="mb-3">

							@if($errors->has('email'))
								<div class="alert alert-danger">{{$errors->first('email')}}</div>
							@endif

							<label for="email" class="form-label">{{ __('Email') }}</label>
							<div class="input-group mb-3">
								<span class="input-group-text"><i class="icon-user"></i></span>
								<input type="email" class="form-control" placeholder="{{ __('Email') }}" name="email" id="email" value="{{ old('email') }}" required>
							</div>
						</div>

						<div class="mb-3">
							<label for="password" class="form-label">{{ __('admin.password') }}</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" onclick="togglePassword()" style="cursor: pointer;">
                                    <i class="icon-eye" id="togglePassword"></i>
                                </span>
                                <input type="password" class="form-control" placeholder="{{ __('admin.password') }}" name="password" id="password" required>
                            </div>

							@if($errors->has('password'))
								<div class="alert alert-danger">{{$errors->first('password') }}</div>
							@endif
						</div>

						@if(config('admin.auth.remember'))
						<div class="mb-3 form-check">
							<input type="checkbox" class="form-check-input" name="remember" id="remember" value="1"  {{ (old('remember')) ? 'checked="checked"' : '' }}>
							<label class="form-check-label" for="remember">{{ __('admin.remember_me') }}</label>
						</div>
						@endif

						<div class="clearfix">
							<button type="submit" class="btn float-end btn-secondary">{{ __('admin.login') }}</button>
						</div>

					</form>
					@endif
				</div>
			</div>
		</div>
	</body>
</html>
