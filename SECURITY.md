# Security Policy

## Reporting a Vulnerability

If you discover a security vulnerability in Boilerworks, please report it responsibly.

**Do not open a public issue.**

Instead, email **security@weareconflict.com** with:

- Description of the vulnerability
- Steps to reproduce
- Potential impact
- Suggested fix (if any)

We will acknowledge your report within 48 hours and aim to release a fix within 7 days for critical issues.

## Supported Versions

| Version | Supported |
| ------- | --------- |
| latest  | Yes       |

## Security Best Practices

When deploying Boilerworks:

- Change all default credentials (database, Redis, MinIO, seeded users)
- Use HTTPS in production
- Set `APP_ENV=production` and `APP_DEBUG=false`
- Generate a fresh `APP_KEY` (`php artisan key:generate`) and keep it secret
- Set `SESSION_ENCRYPT=true` and restrict `SESSION_DOMAIN` to your domain
- Keep dependencies current — CI runs `composer audit` on every push
