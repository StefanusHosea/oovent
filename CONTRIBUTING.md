# Contributing to OOvent

Thank you for considering contributing to OOvent! This document provides guidelines for contributing to the project.

## Getting Started

1. Fork the repository
2. Clone your fork: `git clone https://github.com/YOUR_USERNAME/oovent.git`
3. Create a new branch: `git checkout -b feature/your-feature-name`
4. Make your changes
5. Test your changes thoroughly
6. Commit your changes: `git commit -m "Add your feature"`
7. Push to your fork: `git push origin feature/your-feature-name`
8. Create a Pull Request

## Development Setup

### Requirements
- Local WordPress installation
- WooCommerce plugin installed
- PHP 7.4 or higher
- MySQL/MariaDB database

### Installation for Development
1. Clone the repository into your `wp-content/plugins` directory
2. Activate the plugin in WordPress
3. Make sure WooCommerce is active
4. Create test event products

## Code Standards

### PHP
- Follow WordPress Coding Standards
- Use meaningful variable and function names
- Add comments for complex logic
- Sanitize and validate all user inputs
- Escape all output

### JavaScript
- Use jQuery for compatibility
- Add comments for complex functions
- Follow WordPress JavaScript standards

### CSS
- Use meaningful class names
- Follow BEM methodology where appropriate
- Ensure mobile responsiveness

## Testing

Before submitting a pull request:
1. Test the plugin with the latest WordPress version
2. Test with the latest WooCommerce version
3. Check for PHP errors and warnings
4. Test on different browsers
5. Test mobile responsiveness
6. Verify all features work as expected

## Pull Request Guidelines

- Provide a clear description of the changes
- Reference any related issues
- Include screenshots for UI changes
- Ensure code follows standards
- Update documentation if needed

## Reporting Bugs

When reporting bugs, please include:
- WordPress version
- WooCommerce version
- PHP version
- Browser and version (if frontend issue)
- Steps to reproduce
- Expected behavior
- Actual behavior
- Screenshots if applicable

## Feature Requests

We welcome feature requests! Please:
- Check if the feature already exists
- Provide a clear use case
- Explain how it would benefit users
- Be open to discussion

## Code of Conduct

- Be respectful and inclusive
- Welcome newcomers
- Focus on constructive feedback
- Help others learn

## License

By contributing, you agree that your contributions will be licensed under the GPL v2 or later license.
