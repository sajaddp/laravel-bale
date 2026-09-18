# Contributor Guidance

- Support Laravel 13 only and PHP 8.3+.
- Prefer Laravel-native APIs and keep the runtime architecture minimal.
- Do not add speculative abstractions or future-phase features.
- Bale documentation is authoritative for Bale behavior; never assume Telegram compatibility.
- Test observable public behavior with Laravel HTTP fakes.
- Update the Boost guideline and Bale skill whenever public package behavior changes.
- Run `composer test` for full validation.
