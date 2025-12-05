<?php

namespace Boctulus\Simplerest\libs;

use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\core\libs\Fragment;
use Boctulus\Simplerest\core\libs\ViewModel;

/*
    https://claude.ai/chat/2da02df7-b710-49b0-b42a-17a435233b49

    "View" deberia ser el equivalente a "Activity" en Android o sea para "pantalla completa"
    "Fragment" seria igual que en Android
*/
class UserProfileFragment extends Fragment 
{
    private $userViewModel;
    
    public function onCreate() {
        parent::onCreate();
        
        // Inicializar el ViewModel
        $this->userViewModel = new ViewModel();
        
        // Establecer datos iniciales
        $this->userViewModel->setValue('username', '');
        $this->userViewModel->setValue('email', '');
        $this->userViewModel->setValue('isLoading', false);
        
        // Observar cambios en los datos
        $this->userViewModel->observe('username', function($newValue) {
            // Actualizar la UI cuando cambie el username
            $this->updateUsernameDisplay($newValue);
        });
        
        // Cargar datos del usuario
        $this->loadUserData();
    }
    
    private function loadUserData() {
        $this->userViewModel->setValue('isLoading', true);
        
        // Simulación de carga de datos
        $userId = $_GET['user_id'] ?? 1;
        $userService = new UserService();
        
        $userService->getUserById($userId, function($userData) {
            $this->userViewModel->setValue('username', $userData->username);
            $this->userViewModel->setValue('email', $userData->email);
            $this->userViewModel->setValue('isLoading', false);
        });
    }
    
    private function updateUsernameDisplay($username) {
        // En una implementación real, esto actualizaría el DOM
        // Por ejemplo con una llamada AJAX o WebSocket
    }
    
    public function render() {
        $username = $this->userViewModel->getValue('username');
        $email = $this->userViewModel->getValue('email');
        $isLoading = $this->userViewModel->getValue('isLoading');
        
        if ($isLoading) {
            return '<div class="user-profile loading">Cargando...</div>';
        }
        
        return '
        <div class="user-profile">
            <h2>' . htmlspecialchars($username) . '</h2>
            <p>Email: ' . htmlspecialchars($email) . '</p>
            <button id="edit-profile">Editar perfil</button>
        </div>';
    }
    
    public function handleEvents() {
        // Configurar manejadores de eventos para botones, etc.
        // Por ejemplo, conectar el botón "Editar perfil" con una acción
    }
}
