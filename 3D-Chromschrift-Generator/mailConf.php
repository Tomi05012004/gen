<?php
declare(strict_types=1);

/**
 * Central Mail Configuration for Chrome Generator
 * SMTP settings for sending order confirmations
 * 
 * SECURITY NOTE: The password is currently hardcoded for compatibility.
 * For production use, consider using environment variables:
 * 
 * Example with environment variable:
 * 'password' => getenv('SMTP_PASSWORD') ?: 'fallback_password',
 * 
 * Or using a .env file with vlucas/phpdotenv library
 */

return [
    'host' => 'smtp.strato.de',
    'username' => 'bestellung@chrombeschriftung.de',
    'password' => 'MBDim140212', // TODO: Move to environment variable for production
    'from' => 'bestellung@chrombeschriftung.de',
    'fromName' => 'MBD-ChromShop',
    'replyTo' => 'bestellung@chrombeschriftung.de',
    'port' => 587,
    'secure' => 'tls',
    'charset' => 'utf-8',
    'encoding' => 'quoted-printable',
    'language' => 'de',
    'debug' => 0, // 0=off, 1=client, 2=server, 3=connection, 4=lowlevel
    'distributorMail' => 'bestellung@chrombeschriftung.de',
    'distributorName' => 'MBD-ChromShop'
];
