<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Set Semula Kata Laluan – {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('{{ asset('images/jupem-bg.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
        }
        
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: inherit;
            filter: blur(2px);
            z-index: -1;
        }
        
        .reset-container {
            background-color: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            padding: 2.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 450px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .reset-header {
            text-align: center;
            margin-bottom: 1.8rem;
        }
        
        .reset-header h2 {
            color: #2c3e50;
            font-weight: 700;
            font-size: 1.8rem;
        }
        
        .form-control {
            height: 48px;
            border-radius: 6px;
            margin-bottom: 1.2rem;
            border: 1px solid #ddd;
        }
        
        .btn-reset {
            background-color: #4e73df;
            border: none;
            color: white;
            padding: 12px 0;
            width: 100%;
            border-radius: 6px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            margin-top: 0.5rem;
        }
        
        .btn-reset:hover {
            background-color: #3a5bd9;
            transform: translateY(-2px);
        }
        
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
        }
        
        .invalid-feedback {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: -0.5rem;
            margin-bottom: 1rem;
        }
        
        .password-requirements {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <h2>Set Kata Laluan Baru</h2>
            <p class="text-muted">Sistem Pengurusan Fail Teknikal</p>
        </div>
        
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('simple.password.update') }}">
            @csrf
            
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            
            <div class="mb-3">
                <label for="email_display" class="form-label">Alamat Emel</label>
                <input 
                    type="email" 
                    class="form-control" 
                    id="email_display" 
                    value="{{ $email }}"
                    disabled
                >
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Kata Laluan Baru</label>
                <input 
                    type="password" 
                    class="form-control @error('password') is-invalid @enderror" 
                    id="password" 
                    name="password" 
                    placeholder="Masukkan kata laluan baru"
                    required
                    autofocus
                >
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <div class="password-requirements">
                    Kata laluan mestilah sekurang-kurangnya 8 aksara
                </div>
            </div>
            
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Sahkan Kata Laluan</label>
                <input 
                    type="password" 
                    class="form-control" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    placeholder="Masukkan semula kata laluan baru"
                    required
                >
            </div>
            
            <button type="submit" class="btn btn-reset">Kemas Kini Kata Laluan</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Real-time password confirmation validation
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        
        function validatePasswords() {
            if (password.value !== passwordConfirmation.value) {
                passwordConfirmation.setCustomValidity('Kata laluan tidak sepadan');
            } else {
                passwordConfirmation.setCustomValidity('');
            }
        }
        
        password.addEventListener('input', validatePasswords);
        passwordConfirmation.addEventListener('input', validatePasswords);
    </script>
</body>
</html>
