<?php 
declare(strict_types=1);

namespace Cubo\Eng;

// PHP session wrapper
class Session
{
    public function __construct(private int $lifetime) {}

    public function start(): void
    {
        if ($_ENV['ENVIRONMENT'] === 'prod') {
            // Set cookie parameters to enhance security  
            session_set_cookie_params([
                'secure' => true, // Send cookie over HTTPS only  
                'httponly' => true, // Prevent JavaScript access to cookies  
                'samesite' => 'Strict' // Prevent cross-site request forgery (CSRF)  
            ]);
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($this->timeOut($this->lifetime) ){
            $this->destroy();
            session_start(); // Restart the session if timed out
        } 

        $_SESSION['last_activity'] = time(); // Update last activity timestamp
    }

    public function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            session_unset();

            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 72600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
            session_regenerate_id(true); // Regenerate session ID to prevent session fixation
            
            // Destroy the session
            session_destroy();
        }
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    public function delete(string $key): void
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public function isset(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function timeOut(int $lifetime): bool
    {
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $lifetime)) {
            return true;
        }

        return false;
    }

}