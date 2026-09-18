# Contributor Guidance

- Support Laravel 13 only and PHP 8.3+.
- Prefer Laravel-native APIs and keep the runtime architecture minimal.
- Do not add speculative abstractions or future-phase features.
- Bale documentation is authoritative for Bale behavior; never assume Telegram compatibility.
- Test observable public behavior with Laravel HTTP fakes.
- Update the Boost guideline and Bale skill whenever public package behavior changes.
- Convenience methods must compose existing primitives and remove a repeated, error-prone workflow; do not add framework-style abstractions.
- Testing features must preserve real BaleClient behavior and must not make nonexistent APIs succeed.
- Keep the README, Facade annotations, and Boost resources synchronized with every public API change.
- Run `composer test` for full validation.
