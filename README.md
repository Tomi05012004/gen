# 3D Chrome Text Generator

A web-based generator for creating 3D chrome text designs.

## Requirements

- **PHP Version**: 7.0 - 7.4 (PHP 8.0+ is not supported)
- Web server (Apache/Nginx)
- PHP Extensions:
  - ctype
  - filter
  - hash
  - mbstring (recommended)

## Installation

1. Clone this repository
2. Ensure your server is running PHP 7.x (7.0-7.4)
3. Point your web server to the `3D-Chromschrift-Generator` directory
4. Access via web browser

## PHP Version Note

This project is designed to work with PHP 7.x and explicitly does not support PHP 8.0 or higher. The code uses:
- Type declarations (`declare(strict_types=1)`)
- Null coalescing operator (`??`)
- Scalar type hints

All of these features are available in PHP 7.0+.

**Note**: While the requirement mentions "PHP 6", PHP 6 was never officially released. The PHP version sequence went from 5.x to 7.0. This project targets PHP 7.x compatibility.

## Components

- **3D-Chromschrift-Generator**: Main application
- **PHPMailer-6.10.0**: Email library for order confirmations

## License

Proprietary
