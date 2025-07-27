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
        
        .reset-description {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 1.5rem;
            text-align: center;
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
            margin-top: -0.5rem;
            margin-bottom: 1rem;
        }
        
        .password-requirements ul {
            margin: 0.5rem 0 0 1rem;
            padding: 0;
        }
        
        .password-requirements li {
            margin-bottom: 0.2rem;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <h2>Tukar Kata Laluan</h2>
            <p class="text-muted">Sistem Pengurusan Fail Teknikal</p>
        </div>
        
        <div class="reset-description">
            Masukkan kata laluan baharu anda di bawah. Pastikan kata laluan yang dipilih adalah kuat dan selamat.
        </div>
        
        @if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
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
        
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            
            <div class="mb-3">
                <label for="email" class="form-label">Alamat Emel</label>
                <input 
                    type="email" 
                    class="form-control @error('email') is-invalid @enderror" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', $request->email) }}"
                    placeholder="Alamat emel anda" 
                    required
                    autofocus
                    readonly
                >
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Kata Laluan Baharu</label>
                <input 
                    type="password" 
                    class="form-control @error('password') is-invalid @enderror" 
                    id="password" 
                    name="password" 
                    placeholder="Masukkan kata laluan baharu" 
                    required
                >
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <div class="password-requirements">
                    <strong>Keperluan kata laluan:</strong>
                    <ul>
                        <li>Sekurang-kurangnya 8 aksara</li>
                        <li>Mengandungi huruf besar dan kecil</li>
                        <li>Mengandungi sekurang-kurangnya satu nombor</li>
                        <li>Mengandungi sekurang-kurangnya satu simbol</li>
                    </ul>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Sahkan Kata Laluan</label>
                <input 
                    type="password" 
                    class="form-control @error('password_confirmation') is-invalid @enderror" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    placeholder="Ulang kata laluan baharu" 
                    required
                >
                @error('password_confirmation')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            
            <button type="submit" class="btn btn-reset">Tukar Kata Laluan</button>
            
            <a href="{{ route('login') }}" class="btn-back">Kembali ke Log Masuk</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Remove alert messages after 8 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 1s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 1000);
            });
        }, 8000);
        
        // Password confirmation validation
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        
        function validatePassword() {
            if (password.value !== passwordConfirmation.value) {
                passwordConfirmation.setCustomValidity('Kata laluan tidak sepadan');
            } else {
                passwordConfirmation.setCustomValidity('');
            }
        }
        
        password.addEventListener('change', validatePassword);
        passwordConfirmation.addEventListener('keyup', validatePassword);
    </script>
</body>
</html>
