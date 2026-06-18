# Environment Configuration

- **Rule**: All sensitive credentials must be stored in `.env`.
- **Rule**: `config/` files should reference `env()` to pull these values.
- Never commit `.env` to version control. Reference `.env.example` for required keys.
