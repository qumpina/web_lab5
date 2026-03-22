<?php
// config.php
define('DB_HOST', 'localhost');
define('DB_USER', 'u82092');
define('DB_PASS', '1557612');
define('DB_NAME', 'u82092');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}

// Проверяем наличие mbstring
if (function_exists('mb_substr')) {
    function utf8_substr($str, $start, $len = null) {
        if ($len === null) {
            return mb_substr($str, $start);
        }
        return mb_substr($str, $start, $len);
    }
} else {
    // Своя реализация для UTF-8
    function utf8_substr($str, $start, $len = null) {
        if ($len === null) {
            $len = strlen($str);
        }
        
        $result = '';
        $current_pos = 0;
        $current_len = 0;
        $i = 0;
        $length = strlen($str);
        
        while ($i < $length && $current_len < $start + $len) {
            $char = $str[$i];
            $char_len = 1;
            
            // Определяем длину UTF-8 символа
            if ((ord($char) & 0x80) == 0) {
                $char_len = 1;
            } elseif ((ord($char) & 0xE0) == 0xC0) {
                $char_len = 2;
            } elseif ((ord($char) & 0xF0) == 0xE0) {
                $char_len = 3;
            } elseif ((ord($char) & 0xF8) == 0xF0) {
                $char_len = 4;
            }
            
            if ($current_len >= $start && $current_len < $start + $len) {
                $result .= substr($str, $i, $char_len);
            }
            
            $current_len++;
            $i += $char_len;
        }
        
        return $result;
    }
}

// Функция для генерации случайного логина
function generateLogin($full_name) {
    // Удаляем лишние пробелы
    $full_name = trim(preg_replace('/\s+/', ' ', $full_name));
    
    // Разбиваем на части по пробелам
    $name_parts = explode(' ', $full_name);
    
    $login = '';
    
    if (count($name_parts) >= 2) {
        // Берем первые 2 буквы имени и фамилии
        $first_name = $name_parts[0];
        $last_name = $name_parts[1];
        
        // Получаем первые 2 символа
        $login .= utf8_substr($first_name, 0, 2);
        $login .= utf8_substr($last_name, 0, 2);
    } else {
        // Если нет фамилии, берем первые 4 символа
        $login .= utf8_substr($full_name, 0, 4);
    }
    
    $login .= rand(100, 999);
    return strtolower($login);
}

// Функция для генерации случайного пароля
function generatePassword($length = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    $password = '';
    $max = strlen($chars) - 1;
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, $max)];
    }
    return $password;
}
?>