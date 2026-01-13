    <?php
    ob_start();

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    ini_set('display_errors', 0);
    error_reporting(E_ALL); 

    header('Content-Type: application/json');

    try {
        require_once '../config/connection.php';
        ob_clean();

        $email = $_POST['email'] ?? '';
        $pass  = $_POST['password'] ?? '';

   
        if ($email === 'guest@berangkatin.com' || (empty($email) && empty($pass))) {
            session_unset(); 

            $_SESSION['session_active'] = 'no';
            $_SESSION['user_name'] = 'Tamu';
            $_SESSION['user_id'] = 0;
            
            setcookie("session_active", "no", time() + 3600, "/");
            setcookie("user_name", "Tamu", time() + 3600, "/");

            echo json_encode([
                "status" => "success", 
                "message" => "Masuk sebagai Tamu", 
                "target" => "dashboard.php"
            ]);
            exit;
        }

        $query = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $query->bind_param("s", $email);
        $query->execute();
        $result = $query->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            if (password_verify($pass, $user['password']) || $pass === $user['password']) {
                session_unset();
                
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['user_name'] = $user['nama'];
                $_SESSION['session_active'] = 'yes';

                setcookie("session_active", "yes", time() + 3600, "/");
                setcookie("user_name", $user['nama'], time() + 3600, "/");
                setcookie("user_id", $user['id_user'], time() + 3600, "/");

                echo json_encode([
                    "status" => "success", 
                    "message" => "Selamat datang " . $user['nama'],
                    "target" => "dashboard.php"
                ]);
                exit;
            }
        }

        echo json_encode(["status" => "error", "message" => "Email atau Password salah"]);

    } catch (Exception $e) {
        ob_clean();
        echo json_encode(["status" => "error", "message" => "Terjadi kesalahan server"]);
    }
    exit;