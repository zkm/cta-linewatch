# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2025-08-23

### Added
- GitHub Pages documentation with interactive API explorer
- OpenAPI specification for API endpoints  
- Automated deployment workflow
- Release automation with GitHub Actions
- Interactive release script for easy version management
- Comprehensive test suite with PHPUnit
- Code quality tools (PHPCS, PHPStan)
- Beautiful API documentation with CTA line styling
- RESTful API endpoints for CTA data:
  - `/arrivals` - Train arrival information
  - `/lines` - Train line information  
  - `/lines/{line}` - Stations for specific lines

### Changed
- Migrated from legacy PHP site to CodeIgniter 4 framework
- Updated project structure and dependencies
- Improved security with proper vulnerability reporting

### Fixed
- Improved code quality with PHPCS and PHPStan integration

## [Legacy Archive] - 2025-08-22

### Note
- Legacy PHP site archived to `legacy-site/` directory
- See `MIGRATION.md` for details of the restructure

---

## Release Types

- **Major (X.0.0)**: Breaking changes that require API consumers to update their code
- **Minor (X.Y.0)**: New features that are backward compatible
- **Patch (X.Y.Z)**: Bug fixes and small improvements that are backward compatible

## How to Release

1. Update this CHANGELOG.md file with your changes
2. Create and push a new tag:
   ```bash
   git tag v1.0.0
   git push origin v1.0.0
   ```
3. The release workflow will automatically create a GitHub release with artifacts
