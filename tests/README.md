# Aura Protocol Unit Tests

This directory contains unit tests for the bullying protocol management system.

## How to run the tests

You can run the tests using the following command:

```bash
php tests/run.php
```

If you have PHPUnit installed via composer:

```bash
vendor/bin/phpunit tests
```
*(Note: Current tests are written for the custom runner but are mostly compatible with PHPUnit structure)*

## Structure

- `ProtocolTestCase.php`: Base class for all tests. Sets up an in-memory SQLite database and provides helper methods.
- `run.php`: Simple test runner script.
- `Protocols/`: Tests for regional protocol logic (Aragon, Murcia, Galicia, etc.).
- `Services/`: Tests for the `ProtocolStateService`.
- `Controllers/`: Tests for the `ProtocolController` and CSRF protection.

## Notes on Testing

- Tests use an in-memory SQLite database (`:memory:`) to ensure they are fast and do not affect production data.
- Reflection is used to inject the test database into the `Database` singleton and to mock the authenticated user in `Auth`.
- CSRF protection is tested by verifying the token validation logic. Because `Csrf::validateRequest()` calls `exit()`, it is difficult to test the full controller endpoint in the same process without a more advanced setup.
