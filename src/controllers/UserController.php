<?php
class UserController extends Controller {
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }
    
    public function index() {
        $this->requireAuth();
        
        if ($_SESSION['role'] !== 'admin') {
            $this->view('errors/403', [
                'message' => 'Tu no tienes permiso para acceder a esta página'
            ]);
            return;
        }
        
        $users = $this->userModel->getAll();
        
        $this->view('users/index', [
            'users' => $users
        ]);
    }
    
    public function create() {
        $this->requireAdmin();
        
        $this->view('users/create');
    }
    
    public function store() {
        $this->requireAdmin();
        
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $role = $_POST['role'] ?? '';
        
        $errors = [];
        
        if (empty($username)) {
            $errors['username'] = 'El nombre de usuario es obligatorio';
        } elseif ($this->userModel->getByUsername($username)) {
            $errors['username'] = 'El nombre de usuario ya existe';
        }
        
        if (empty($email)) {
            $errors['email'] = 'El correo electrónico es obligatorio';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Formato de correo electrónico no válido';
        } elseif ($this->userModel->getByEmail($email)) {
            $errors['email'] = 'El correo electrónico ya existe';
        }
        
        if (empty($password)) {
            $errors['password'] = 'La contraseña es obligatoria';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        if ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Las contraseñas no coinciden';
        }
        
        if (empty($firstName)) {
            $errors['first_name'] = 'El nombre es obligatorio';
        }
        
        if (empty($lastName)) {
            $errors['last_name'] = 'El apellido es obligatorio';
        }
        
        if (empty($role) || !in_array($role, ['admin', 'user'])) {
            $errors['role'] = 'El rol es obligatorio y debe ser válido';
        }
        
        if (!empty($errors)) {
            $this->view('users/create', [
                'errors' => $errors,
                'user' => $_POST
            ]);
            return;
        }
        
        $userId = $this->userModel->create([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'role' => $role
        ]);
        
        $_SESSION['success'] = 'Usuario creado correctamente';
        $this->redirect('/users');
    }
    
    public function edit($id) {
        $this->requireAdmin();
        
        if (is_array($id)) {
            if (isset($id['id'])) {
                $id = $id['id'];
            } elseif (isset($id[0])) {
                $id = $id[0];
            } else {
                $id = 0;
            }
        }

        $id = (int)$id;
        
        $user = $this->userModel->getById($id);
        
        if (!$user) {
            $this->view('errors/404', [
                'message' => 'Usuario no encontrado'
            ]);
            return;
        }
        
        $this->view('users/edit', [
            'user' => $user
        ]);
    }
    
    public function update($id) {
        $this->requireAdmin();
        
        if (is_array($id)) {
            if (isset($id['id'])) {
                $id = $id['id'];
            } elseif (isset($id[0])) {
                $id = $id[0];
            } else {
                $id = 0;
            }
        }
        
        $id = (int)$id;
        
        $user = $this->userModel->getById($id);
        
        if (!$user) {
            $this->view('errors/404', [
                'message' => 'Usuario no encontrado'
            ]);
            return;
        }
        
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $role = $_POST['role'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        $errors = [];
        
        if (empty($username)) {
            $errors['username'] = 'El nombre de usuario es obligatorio';
        } elseif ($username !== $user['username'] && $this->userModel->getByUsername($username)) {
            $errors['username'] = 'El nombre de usuario ya existe';
        }
        
        if (empty($email)) {
            $errors['email'] = 'El correo electrónico es obligatorio';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Formato de correo electrónico no válido';
        } elseif ($email !== $user['email'] && $this->userModel->getByEmail($email)) {
            $errors['email'] = 'El correo electrónico ya existe';
        }
        
        if (empty($firstName)) {
            $errors['first_name'] = 'El nombre es obligatorio';
        }
        
        if (empty($lastName)) {
            $errors['last_name'] = 'El apellido es obligatorio';
        }
        
        if (empty($role) || !in_array($role, ['admin', 'user'])) {
            $errors['role'] = 'El rol es obligatorio y debe ser válido';
        }
    
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $errors['password'] = 'La contraseña debe tener al menos 6 caracteres';
            }
            
            if ($password !== $confirmPassword) {
                $errors['confirm_password'] = 'Las contraseñas no coinciden';
            }
        }
        
        if (!empty($errors)) {
            $this->view('users/edit', [
                'errors' => $errors,
                'user' => array_merge($user, $_POST)
            ]);
            return;
        }
        
        $this->userModel->update($id, [
            'username' => $username,
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'role' => $role
        ]);
        
        if (!empty($password)) {
            $this->userModel->updatePassword($id, $password);
        }
        
        $_SESSION['success'] = 'Usuario actualizado correctamente';
        $this->redirect('/users');
    }
    
    public function toggleStatus($id) {
        $this->requireAdmin();
        
        if (is_array($id)) {
            if (isset($id['id'])) {
                $id = $id['id'];
            } elseif (isset($id[0])) {
                $id = $id[0];
            } else {
                $id = 0;
            }
        }
        
        $id = (int)$id;
        
        $user = $this->userModel->getById($id);
        
        if (!$user) {
            $this->view('errors/404', [
                'message' => 'Usuario no encontrado'
            ]);
            return;
        }
        
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = 'No puedes bloquearte a ti mismo';
            $this->redirect('/users');
            return;
        }
        
        if ($user['status'] === 'active') {
            $this->userModel->block($id);
            $_SESSION['success'] = 'Usuario bloqueado correctamente';
        } else {
            $this->userModel->unblock($id);
            $_SESSION['success'] = 'Usuario desbloqueado correctamente';
        }
        
        $this->redirect('/users');
    }
    
    public function block($id) {
        return $this->toggleStatus($id);
    }
    
    public function unblock($id) {
        return $this->toggleStatus($id);
    }
}