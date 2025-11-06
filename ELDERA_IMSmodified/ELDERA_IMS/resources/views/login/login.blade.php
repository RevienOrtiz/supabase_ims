<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <title>ELDERA - Log In</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
        margin: 0;
        font-family: "Poppins", sans-serif;
        background-color: rgb(42, 44, 41);
        min-height: 100vh;
        overflow-x: hidden;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .logo {
        position: absolute;
        top: 2rem;
        left: 2rem;
        width: clamp(60px, 8vw, 100px);
        height: clamp(60px, 8vw, 100px);
        max-width: 15vw;
        border: 2px solid #e31575;
        border-radius: 50%;
        background-color: #e31575;
        object-fit: cover;
    }

    /* Layout */
    main {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 120px);
        text-align: center;
        padding: clamp(0.5rem, 2vw, 1rem);
    }

    .container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 0 clamp(1rem, 3vw, 2rem);
    }

    /* Form Styles */
    .form-container {
        margin-bottom: 2rem;
    }

    .form-title {
        font-size: clamp(1.4rem, 4.5vw, 2.5rem);
        font-weight: 900;
        color: #e31575;
    }

    .form-subtitle {
        font-size: clamp(1.4rem, 4.5vw, 2.3rem);
        font-weight: 800;
        color: #333;
    }

    .login-form {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 1.2rem;
        min-width: clamp(280px, 80vw, 320px);
    }

    .form-label {
        font-size: clamp(1.0rem, 2.0vw, 1rem);
        font-weight: 600;
        color: #111;
        display: flex;
        align-items: center;
        gap: 1rem;
        width: 100%;
    }

    .form-input {
        flex: 1;
        padding: 10px 10px;
        width: clamp(280px, 60vw, 380px);
        border-radius: 6px;
        border: 1px solid #e31575;
        background-color: white;
        outline: none;
        font-size: clamp(1rem, 2vw, 1rem);
        margin-left: 1.5rem;
    }

    .forgot-password {
        color: #1a237e;
        font-size: clamp(0.9rem, 2vw, 1rem);
        text-align: center;
        width: 100%;
        margin-top: 0.5rem;
        text-decoration: none;
    }

    /* Password toggle styling */
    .password-toggle-inline {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #666;
        font-size: 16px;
        z-index: 10;
        transition: all 0.3s ease;
        padding: 5px;
    }

    .password-toggle-inline:hover {
        color: #e31575;
        transform: translateY(-50%) scale(1.1);
    }

    .password-toggle-inline i {
        transition: all 0.3s ease;
    }

    /* Responsive Button Styles - REPLACE the existing button styles */
    .button-group {
        
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: clamp(0.5rem, 2vw, 1rem);
    }

    .button {
        padding: clamp(0.8rem, 2vw, 1rem) clamp(1.5rem, 3vw, 2rem);
        border: none;
        border-radius: 20px;
        font-size: clamp(0.9rem, 2.5vw, 1.1rem);
        font-weight: 500;
        cursor: pointer;
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
        width: clamp(150px, 25vw, 200px);
        height: clamp(40px, 8vw, 50px);
        transition: transform 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 100px;
    }

    .button:hover {
        transform: translateY(-2px);
    }

    .login-button {
        background-color: #e31575;
        color: white;
        font-weight: 700;
    }

    .signup-button {
        background-color: #333;
        color: white;
        font-weight: 700;
    }

    /* Footer */
    .footer-logo {
        position: absolute;
        bottom: clamp(0.5rem, 2vw, 2rem);
        right: clamp(1rem, 3vw, 2rem);
        width: clamp(60px, 8vw, 100px);
        max-width: 15vw;
        height: auto;
        filter: drop-shadow(0 0 0 white) drop-shadow(0 0 2px white);
        z-index: 1;
    }


        /* Header Styles */
        .header_container {
            z-index: 2;
        }
        
        .header {
            padding: 2rem;
            text-align: center;
        }

        .menu-icon {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            font-size: 1.5rem;
            color: rgb(0, 0, 0);
            z-index: 4;
            cursor: pointer;
        }

        /* Background Elements */
        #circle1 {
        height: 100vh;
        width: 200vw;
        position: absolute;
        background: #FFB7CE;
        border-radius: 50%;
        bottom: 10%;
        left: -70%;
        border: 8px solid #e31575;
        z-index: -2;
    }

    #circle2 {
        height: 120vh;
        width: 200vw;
        position: absolute;
        background: white;
        border-radius: 50%;
        bottom: 14%;
        left: -72%;
        z-index: -1;
    }


    </style>
</head>

<body>
     <img src="{{asset('images/LCSCF_LOGO.png')}}" alt="LCSCF Logo" class="logo">
    <main>
        <div class="container">
            <div class="form-container">
                <div class="form-title">SENIOR CITIZEN</div>
                <div class="form-subtitle">INFORMATION MANAGEMENT SYSTEM</div>
            </div>
            <form action="{{ route('login.post') }}" method="POST" class="login-form">
                @csrf
                @if($errors->any())
                    <div class="alert alert-danger" style="color: red; margin-bottom: 1rem; padding: 0.5rem; background-color: #ffe6e6; border: 1px solid red; border-radius: 4px;">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if(session('message'))
                    <div class="alert alert-info" style="color: #1a237e; margin-bottom: 1rem; padding: 0.5rem; background-color: #e3f2fd; border: 1px solid #1a237e; border-radius: 4px;">
                        {{ session('message') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success" style="color: green; margin-bottom: 1rem; padding: 0.5rem; background-color: #e6ffe6; border: 1px solid green; border-radius: 4px;">
                        {{ session('success') }}
                    </div>
                @endif
                
                <div class="mb-3">
                    <label class="form-label">EMAIL:</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background-color: #e31575; color: white;"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-input" name="email" placeholder="EMAIL" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">PASSWORD:</label>
                    <div class="input-group" style="position: relative;">
                        <span class="input-group-text" style="background-color: #e31575; color: white;"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-input" name="password" id="password" placeholder="PASSWORD" required style="padding-right: 45px;">
                        <span class="password-toggle-inline" id="togglePassword">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="button-group" style="display: flex; justify-content: center; width: 100%;">
                    <button type="submit" class="button login-button">LOGIN</button>
                   
                    {{-- <a href="{{ route('signup') }}" style="text-decoration: none;">
                        <button type="button" class="button signup-button">SIGN UP</button>
                    </a> --}}
                </div>
                <a href="#" class="forgot-password">Forgot Password?</a>
            </form>
        </div>
    </main>

    <div id="circle1"></div>
    <div id="circle2"></div>
    <div><img src="{{asset('images/Bagong_Pilipinas.png')}}" alt="Bagong Pilipinas" class="footer-logo"></div>

    <script>
        // Password visibility toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            togglePassword.addEventListener('click', function() {
                // Toggle the type attribute
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle the eye icon
                if (type === 'password') {
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                } else {
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                }
            });
        });
    </script>
</body>
</html>
