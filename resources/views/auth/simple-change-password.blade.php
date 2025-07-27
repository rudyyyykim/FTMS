<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tukar Kata Laluan – {{ config('app.name') }}</title>
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
        
        .change-container {
            background-color: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            padding: 2.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 450px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .change-header {
            text-align: center;
            margin-bottom: 1.8rem;
        }
        
        .change-header h2 {
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
        
        .btn-change {
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
        
        .btn-change:hover {
            background-color: #3a5bd9;
            transform: translateY(-2px);
        }
        
        .btn-back {
            background-color: #6c757d;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            margin-top: 1rem;
            width: 100%;
            text-align: center;
        }
        
        .btn-back:hover {
            background-color: #545b62;
            color: white;
            text-decoration: none;
        }
        
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
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
            margin-top: -0.8rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="change-container">
        <div class="change-header">
            <h2>Tukar Kata Laluan</h2>
            <p class="text-muted">Sistem Pengurusan Fail Teknikal</p>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('simple.password.change') }}">
            @csrf
            
            <div class="mb-3">
                <label for="email" class="form-label">Alamat Emel</label>
                <input 
                    type="email" 
                    class="form-control @error('email') is-invalid @enderror" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    placeholder="Masukkan emel anda" 
                    required
                    autofocus
                >
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="current_password" class="form-label">Kata Laluan Semasa</label>
                <input 
                    type="password" 
                    class="form-control @error('current_password') is-invalid @enderror" 
                    id="current_password" 
                    name="current_password" 
                    placeholder="Masukkan kata laluan semasa"
                    required
                >
                @error('current_password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="new_password" class="form-label">Kata Laluan Baru</label>
                <input 
                    type="password" 
                    class="form-control @error('new_password') is-invalid @enderror" 
                    id="new_password" 
                    name="new_password" 
                    placeholder="Masukkan kata laluan baru"
                    required
                >
                @error('new_password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <div class="password-requirements">
                    Kata laluan mestilah sekurang-kurangnya 8 aksara
                </div>
            </div>
            
            <div class="mb-3">
                <label for="new_password_confirmation" class="form-label">Sahkan Kata Laluan Baru</label>
                <input 
                    type="password" 
                    class="form-control" 
                    id="new_password_confirmation" 
                    name="new_password_confirmation" 
                    placeholder="Masukkan semula kata laluan baru"
                    required
                >
            </div>
            
            <button type="submit" class="btn btn-change">Tukar Kata Laluan</button>
            
            <a href="{{ route('login') }}" class="btn-back">Kembali ke Log Masuk</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Real-time password confirmation validation
        const newPassword = document.getElementById('new_password');
        const passwordConfirmation = document.getElementById('new_password_confirmation');
        
        function validatePasswords() {
            if (newPassword.value !== passwordConfirmation.value) {
                passwordConfirmation.setCustomValidity('Kata laluan tidak sepadan');
            } else {
                passwordConfirmation.setCustomValidity('');
            }
        }
        
        newPassword.addEventListener('input', validatePasswords);
        passwordConfirmation.addEventListener('input', validatePasswords);
        
        // Auto-hide success messages after 5 seconds
        setTimeout(() => {
            const successAlert = document.querySelector('.alert-success');
            if (successAlert) {
                successAlert.style.transition = 'opacity 1s';
                successAlert.style.opacity = '0';
                setTimeout(() => successAlert.remove(), 1000);
            }
        }, 5000);
    </script>
</body>
</html>
