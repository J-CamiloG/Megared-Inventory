<?php
class AuthController extends Controller {
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }
    
    public function loginForm() {
        if ($this->isAuthenticated()) {
            $this->redirect('/dashboard');
        }
        $this->view('auth/login');
    }

    public function login() {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $user = $this->userModel->getByUsername($username);

        if ($user) {
            if ($user['status'] === 'blocked') {
                $this->view('auth/login', [
                    'error' => 'Tu cuenta está bloqueada. Por favor, contacta al administrador para desbloquearla.',
                    'username' => $username
                ]);
                return;
            }
            $passwordVerified = password_verify($password, $user['password']);
        }
        
        if ($user && password_verify($password, $user['password'])) {

            if ($user['failed_login_attempts'] > 0) {
                $this->userModel->resetLoginAttempts($user['id']);
            }
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();

            $this->redirect('/dashboard');
        } else {

            if ($user) {
                $attempts = $this->userModel->incrementLoginAttempts($user['id']);

                $maxAttempts = 3;
                if ($attempts >= $maxAttempts) {
                    $this->userModel->block($user['id']);

                    $this->view('auth/login', [
                        'error' => 'Tu cuenta ha sido bloqueada debido a múltiples intentos fallidos. Por favor, contacta al administrador.',
                        'username' => $username
                    ]);
                    return;
                }
                
                $remainingAttempts = $maxAttempts - $attempts;
                $this->view('auth/login', [
                    'error' => "Usuario o contraseña incorrectos. Te quedan $remainingAttempts intento(s).",
                    'username' => $username
                ]);
            } else {
                $this->view('auth/login', [
                    'error' => 'Usuario o contraseña incorrectos.',
                    'username' => $username
                ]);
            }
        }

    }

    public function logout() {
        session_destroy();
        $this->redirect('/login');
    }
    
    public function forgotPasswordForm() {
        $this->view('auth/forgot-password');
    }
    
    public function forgotPassword() {
        $email = $_POST['email'] ?? '';
        
        if (empty($email)) {
            $this->view('auth/forgot-password', [
                'error' => 'El email es requerido'
            ]);
            return;
        }
        
        $user = $this->userModel->getByEmail($email);
        
        if (!$user) {
            $this->view('auth/forgot-password', [
                'error' => 'No existe una cuenta con ese email'
            ]);
            return;
        }
        
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $this->userModel->createResetToken($user['id'], $token, $expiresAt);
        
        $resetLink = APP_URL . '/reset-password?token=' . $token;
        
        $this->view('auth/forgot-password-success', [
            'resetLink' => $resetLink
        ]);
    }
    
    public function resetPasswordForm() {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            $this->redirect('/login');
        }

        $tokenData = $this->userModel->getResetToken($token);
        
        if (!$tokenData) {
            $this->view('auth/reset-password', [
                'error' => 'token no válido o expirado',
                'token' => $token,
                'valid' => false
            ]);
            return;
        }
        
        $this->view('auth/reset-password', [
            'token' => $token,
            'valid' => true
        ]);
    }
    
    public function resetPassword() {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($token) || empty($password) || empty($confirmPassword)) {
            $this->view('auth/reset-password', [
                'error' => 'Todos los campos son requeridos',
                'token' => $token,
                'valid' => true
            ]);
            return;
        }
        
        if ($password !== $confirmPassword) {
            $this->view('auth/reset-password', [
                'error' => 'Contraseñas no coinciden',
                'token' => $token,
                'valid' => true
            ]);
            return;
        }
        
        $tokenData = $this->userModel->getResetToken($token);
        
        if (!$tokenData) {
            $this->view('auth/reset-password', [
                'error' => 'Token no válido o expirado',
                'token' => $token,
                'valid' => false
            ]);
            return;
        }
        
        $this->userModel->updatePassword($tokenData['user_id'], $password);
        
        $this->userModel->deleteResetToken($token);
        
        $_SESSION['success'] = 'Contraseña restablecida con éxito. Puedes iniciar sesión ahora.';
        $this->redirect('/login');
    }
}