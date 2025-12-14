<?php
/**
 * UFUTURO Licenciado - Configuração da Base de Dados
 * 
 * IMPORTANTE: Altere estas credenciais antes de fazer deploy!
 * Em produção, considere usar variáveis de ambiente.
 */

// Configurações da base de dados MySQL (Hostinger)
define('DB_HOST', 'localhost');
define('DB_NAME', 'u979854804_UfuturoDb'); // Altere para o nome da sua base de dados
define('DB_USER', 'u979854804_jessicatcheco3'); // Altere para o seu utilizador
define('DB_PASS', 'Yeshuaeocaminho1'); // Altere para a sua palavra-passe

// Configurações de sessão
define('SESSION_NAME', 'ufuturo_session');
define('SESSION_LIFETIME', 86400 * 7); // 7 dias

// Configurações CORS (altere para o seu domínio em produção)
define('ALLOWED_ORIGINS', [
    'http://localhost:5173',
    'http://localhost:3000',
    'https://ufuturolicenciado.com',
    'https://www.ufuturolicenciado.com'
]);

// Timezone
date_default_timezone_set('Africa/Maputo');

// Error reporting (desactive em produção)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
