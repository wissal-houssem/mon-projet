<?php
// backend/login.php
require_once 'config.php';
require_once 'session.php';

// إذا كان مسجلاً دخول، توجيه للوحة التحكم
if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Veuillez remplir tous les champs';
    } else {
        // التحقق من بيانات المسؤول
        $sql = "SELECT id, username, password, full_name, is_active FROM admins WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            // التحقق من كلمة المرور
            if (password_verify($password, $admin['password'])) {
                if ($admin['is_active']) {
                    // تحديث وقت آخر دخول
                    $update_sql = "UPDATE admins SET last_login = NOW() WHERE id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("i", $admin['id']);
                    $update_stmt->execute();
                    $update_stmt->close();
                    
                    // تسجيل الدخول في الجلسة
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_full_name'] = $admin['full_name'];
                    $_SESSION['login_time'] = time();
                    
                    // توجيه للوحة التحكم
                    header('Location: dashboard.php');
                    exit();
                } else {
                    $error = 'Ce compte est désactivé';
                }
            } else {
                $error = 'Nom d\'utilisateur ou mot de passe incorrect';
            }
        } else {
            $error = 'Nom d\'utilisateur ou mot de passe incorrect';
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Administration PC PortableTech</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            color: #d5acf4;
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .logo p {
            color: #aaa;
            font-size: 14px;
        }
        
        .login-form h2 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            color: #ddd;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            color: white;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #7b3fe4;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 3px rgba(123, 63, 228, 0.3);
        }
        
        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #7b3fe4, #c95bee);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(123, 63, 228, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .error-message {
            background: rgba(244, 67, 54, 0.2);
            color: #ff6b6b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid rgba(244, 67, 54, 0.3);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .login-footer p {
            color: #aaa;
            font-size: 14px;
        }
        
        .login-footer a {
            color: #d5acf4;
            text-decoration: none;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        /* Animation pour le formulaire */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-container {
            animation: fadeIn 0.6s ease-out;
        }
        
        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
            
            .logo h1 {
                font-size: 24px;
            }
            
            .login-form h2 {
                font-size: 20px;
            }
        }
        
        /* Styles pour l'icône */
        .icon-container {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .icon-container i {
            font-size: 48px;
            color: #7b3fe4;
            margin-bottom: 10px;
        }
        
        /* Message de succès après inscription */
        .success-message {
            background: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid rgba(46, 204, 113, 0.3);
        }
        
        /* Loading animation */
        .loading {
            display: none;
            text-align: center;
            margin-top: 10px;
        }
        
        .loading i {
            color: #7b3fe4;
            font-size: 20px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="icon-container">
            <i class="fas fa-laptop-code"></i>
        </div>
        
        <div class="logo">
            <h1><i class="fas fa-laptop"></i> PC PortableTech</h1>
            <p>Administration</p>
        </div>
        
        <div class="login-form">
            <h2>Connexion Administrateur</h2>
            
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['logout']) && $_GET['logout'] == 'success'): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> Déconnecté avec succès
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['expired']) && $_GET['expired'] == 'true'): ?>
                <div class="error-message">
                    <i class="fas fa-clock"></i> Session expirée. Veuillez vous reconnecter.
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="loginForm">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Nom d'utilisateur</label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           placeholder="Entrez votre nom d'utilisateur" 
                           value="<?php echo htmlspecialchars($username); ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Mot de passe</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           placeholder="Entrez votre mot de passe" 
                           required>
                </div>
                
                <button type="submit" class="btn-login" id="submitBtn">
                    <i class="fas fa-sign-in-alt"></i> Se Connecter
                </button>
                
                <div class="loading" id="loading">
                    <i class="fas fa-spinner"></i> Connexion en cours...
                </div>
            </form>
        </div>
        
        <div class="login-footer">
            <p>© <?php echo date('Y'); ?> - PC PortableTech</p>
            <p><small>Accès réservé au personnel autorisé</small></p>
        </div>
    </div>
    
    <script>
        // Ajouter un effet de chargement lors de la soumission
        document.getElementById('loginForm').addEventListener('submit', function() {
            document.getElementById('submitBtn').style.display = 'none';
            document.getElementById('loading').style.display = 'block';
        });
        
        // Effet d'entrée pour les champs
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
        
        // Afficher/masquer le mot de passe (optionnel)
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const showPasswordBtn = document.createElement('button');
            showPasswordBtn.type = 'button';
            showPasswordBtn.innerHTML = '<i class="fas fa-eye"></i>';
            showPasswordBtn.style.cssText = `
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
                background: transparent;
                border: none;
                color: #aaa;
                cursor: pointer;
                font-size: 16px;
            `;
            
            const passwordContainer = passwordInput.parentElement;
            passwordContainer.style.position = 'relative';
            passwordContainer.appendChild(showPasswordBtn);
            
            showPasswordBtn.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            });
        });
        
        // Focus automatique sur le premier champ
        document.getElementById('username').focus();
    </script>
</body>
</html>