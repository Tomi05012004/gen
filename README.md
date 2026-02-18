# 3D Chrome Text Generator

A web-based generator for creating 3D chrome text designs.

## Requirements

- **PHP Version**: 7.x (referred to as "PHP 6" in project requirements, since PHP 6 was never released)
  - Supported: PHP 7.0 - 7.4
  - Not supported: PHP 8.0+
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

## PHP Version Note - "PHP 6" Clarification

**IMPORTANT**: This project requires "PHP 6" according to its specifications. However, **PHP 6 was never officially released by the PHP development team**. 

The PHP version history is:
- PHP 5.x (5.0, 5.1, 5.2, 5.3, 5.4, 5.5, 5.6)
- **PHP 6 - NEVER RELEASED** (abandoned project)
- PHP 7.x (7.0, 7.1, 7.2, 7.3, 7.4) ← **This is what "PHP 6" means in this project**
- PHP 8.x (8.0, 8.1, 8.2, 8.3)

**Therefore, "PHP 6" in this project's requirements refers to PHP 7.x**, which is the version that would have logically followed PHP 5.x if PHP 6 had been released.

This project uses PHP 7.0+ features:
- Type declarations (`declare(strict_types=1)`)
- Null coalescing operator (`??`)
- Scalar type hints
- Return type declarations

All of these features are available in PHP 7.0+ and are **NOT available in PHP 5.6 or earlier**.

### ⚠️ Security Warning

**IMPORTANT**: All PHP 7.x versions (7.0 through 7.4) have reached end-of-life and no longer receive security updates:
- PHP 7.0: EOL December 3, 2018
- PHP 7.1: EOL December 1, 2019
- PHP 7.2: EOL November 30, 2020
- PHP 7.3: EOL December 6, 2021
- PHP 7.4: EOL November 28, 2022

Using an unsupported PHP version exposes your application to known security vulnerabilities. Consider upgrading to PHP 8.1 or later for security and performance improvements, or at minimum ensure your hosting environment applies necessary security patches.

## Components

- **3D-Chromschrift-Generator**: Main application
- **PHPMailer-6.10.0**: Email library for order confirmations

## License

Proprietary
