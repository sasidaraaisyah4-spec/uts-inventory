<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sibu Toy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f3f0ff 0%, #fff0f6 50%, #e6fcf5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            padding: 44px 38px;
            border-radius: 32px;
            border: 2px solid rgba(255, 255, 255, 0.95);
            box-shadow: 0 20px 45px -10px rgba(177, 151, 252, 0.25), 0 10px 25px -5px rgba(247, 131, 172, 0.15);
            width: 100%;
            max-width: 420px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #b197fc 0%, #f783ac 50%, #63e6be 100%);
        }

        .brand-badge {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #e599f7 0%, #da77f2 100%);
            border-radius: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 26px;
            box-shadow: 0 8px 20px rgba(218, 119, 242, 0.35);
            margin-bottom: 18px;
        }

        h2 {
            color: #2b2d42;
            font-size: 23px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .subtitle {
            font-size: 13px;
            color: #868e96;
            margin-bottom: 28px;
            font-weight: 500;
        }

        .error-box {
            background: #fff0f6;
            border: 1.5px solid #ffdeeb;
            color: #e64980;
            padding: 12px 16px;
            border-radius: 16px;
            margin-bottom: 20px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            text-align: left;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 700;
            color: #495057;
            margin-bottom: 8px;
            font-size: 13.5px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            font-size: 15px;
        }

        input {
            width: 100%;
            padding: 13px 18px 13px 44px;
            border: 1.5px solid #f1f3f5;
            border-radius: 18px;
            font-size: 14px;
            outline: none;
            background: #fbfbfc;
            color: #343a40;
            transition: all 0.25s ease;
        }

        input:focus {
            background: white;
            border-color: #b197fc;
            box-shadow: 0 0 0 4px rgba(177, 151, 252, 0.2);
        }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #b197fc 0%, #f783ac 100%);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 15px;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(247, 131, 172, 0.35);
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(247, 131, 172, 0.45);
        }
    </style>
</head>
<body>
    
    <div class="login-card">
        <h2>Sibu Toy</h2>
        <p class="subtitle">Art Toys & Collectibles Official Operations</p>

        @if($errors->any())
            <div class="error-box">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Email Pengguna</label>
                <div class="input-wrap">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@toko.com" required>
                </div>
            </div>

            <div class="form-group">
                <label>Kata Sandi</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn-login">
                Masuk ke Toko
            </button>
        </form>
    </div>

</body>
</html>
