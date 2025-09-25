# Contributing to Laravel Access Tree

Thank you for considering contributing to **Laravel Access Tree**! 🎉  
We welcome bug reports, feature requests, and pull requests that help improve this package.

---

## Coding Standards
- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards.
- Run `composer fix-style` (if you use Laravel Pint or PHP-CS-Fixer) before submitting a pull request.
- Keep code clean, consistent, and well-documented.

---

## Testing
- Ensure all tests pass before submitting a PR.
- Run the test suite with:

```bash
composer test
# or directly
vendor/bin/phpunit
```
## Local Development
To work on this package locally:
1. Clone your fork of the repository.
2. Install dependencies:

```bash
composer install
```

3. Link the package into your Laravel app using a path repository in your composer.json:
```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../access-tree"
    }
  ]
}
```

4. Require it locally:
```bash
composer require obrainwave/access-tree:dev-main
```
Now any changes in your local package will be reflected immediately in your Laravel app.

## Pull Requests
* Fork the repo and create a new branch (feature/my-new-feature or fix/bug-name).
* Write clear commit messages.
* Update documentation if your changes affect usage.
* Submit a PR with a clear description of the changes.

## Security
If you discover a security vulnerability, please do not open an issue.
Instead, email <olaiwolaakeem@gmail.com>

## Thanks
Your contributions make this package better for everyone!