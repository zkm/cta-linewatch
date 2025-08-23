#!/bin/bash

# Release helper script for CTA LineWatch
# Usage: ./scripts/release.sh [major|minor|patch] [version]

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Helper functions
log_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Check if we're in a git repository
if ! git rev-parse --git-dir > /dev/null 2>&1; then
    log_error "Not in a git repository"
    exit 1
fi

# Check if working directory is clean
if [ -n "$(git status --porcelain)" ]; then
    log_error "Working directory is not clean. Please commit or stash your changes."
    git status --short
    exit 1
fi

# Get current version from latest tag
CURRENT_VERSION=$(git tag --sort=-version:refname | head -n 1 | sed 's/^v//')

if [ -z "$CURRENT_VERSION" ]; then
    log_warning "No previous version found. This will be the first release."
    CURRENT_VERSION="0.0.0"
fi

log_info "Current version: $CURRENT_VERSION"

# Parse version components
IFS='.' read -r -a VERSION_PARTS <<< "$CURRENT_VERSION"
MAJOR=${VERSION_PARTS[0]:-0}
MINOR=${VERSION_PARTS[1]:-0}
PATCH=${VERSION_PARTS[2]:-0}

# Determine new version
if [ "$1" = "major" ]; then
    NEW_MAJOR=$((MAJOR + 1))
    NEW_MINOR=0
    NEW_PATCH=0
elif [ "$1" = "minor" ]; then
    NEW_MAJOR=$MAJOR
    NEW_MINOR=$((MINOR + 1))
    NEW_PATCH=0
elif [ "$1" = "patch" ]; then
    NEW_MAJOR=$MAJOR
    NEW_MINOR=$MINOR
    NEW_PATCH=$((PATCH + 1))
elif [ -n "$2" ]; then
    # Custom version provided
    NEW_VERSION="$2"
else
    # Interactive mode
    echo
    log_info "Current version: v$CURRENT_VERSION"
    echo "Choose release type:"
    echo "  1) Patch (v$MAJOR.$MINOR.$((PATCH + 1))) - Bug fixes"
    echo "  2) Minor (v$MAJOR.$((MINOR + 1)).0) - New features"
    echo "  3) Major (v$((MAJOR + 1)).0.0) - Breaking changes"
    echo "  4) Custom version"
    
    read -p "Enter choice [1-4]: " choice
    
    case $choice in
        1)
            NEW_MAJOR=$MAJOR
            NEW_MINOR=$MINOR
            NEW_PATCH=$((PATCH + 1))
            ;;
        2)
            NEW_MAJOR=$MAJOR
            NEW_MINOR=$((MINOR + 1))
            NEW_PATCH=0
            ;;
        3)
            NEW_MAJOR=$((MAJOR + 1))
            NEW_MINOR=0
            NEW_PATCH=0
            ;;
        4)
            read -p "Enter custom version (without 'v' prefix): " NEW_VERSION
            ;;
        *)
            log_error "Invalid choice"
            exit 1
            ;;
    esac
fi

# Construct new version if not custom
if [ -z "$NEW_VERSION" ]; then
    NEW_VERSION="$NEW_MAJOR.$NEW_MINOR.$NEW_PATCH"
fi

NEW_TAG="v$NEW_VERSION"

log_info "New version: $NEW_TAG"

# Confirmation
echo
read -p "Create release $NEW_TAG? [y/N]: " confirm
if [[ ! $confirm =~ ^[Yy]$ ]]; then
    log_info "Release cancelled"
    exit 0
fi

# Check if tag already exists
if git tag | grep -q "^$NEW_TAG$"; then
    log_error "Tag $NEW_TAG already exists"
    exit 1
fi

# Run tests before creating release
log_info "Running tests..."
cd "$PROJECT_ROOT"

if [ -f "vendor/bin/phpunit" ]; then
    composer test || {
        log_error "Tests failed. Please fix the issues before releasing."
        exit 1
    }
    log_success "Tests passed"
else
    log_warning "PHPUnit not found, skipping tests"
fi

# Update CHANGELOG.md
log_info "Updating CHANGELOG.md..."
DATE=$(date +%Y-%m-%d)
TEMP_FILE=$(mktemp)

# Add new version entry to changelog
{
    sed '/^## \[Unreleased\]/,/^## / {
        /^## \[Unreleased\]/r /dev/stdin
    }' CHANGELOG.md << EOF

## [$NEW_VERSION] - $DATE

### Added
- Release $NEW_VERSION

EOF
} > "$TEMP_FILE"

mv "$TEMP_FILE" CHANGELOG.md

# Commit changelog update
git add CHANGELOG.md
git commit -m "chore: update CHANGELOG.md for release $NEW_TAG"

# Create and push tag
log_info "Creating tag $NEW_TAG..."
git tag -a "$NEW_TAG" -m "Release $NEW_TAG"

log_info "Pushing tag to origin..."
git push origin "$NEW_TAG"
git push origin master

log_success "Release $NEW_TAG created successfully!"
log_info "GitHub Actions will now build and create the release automatically."
log_info "Check the Actions tab in your GitHub repository for progress."

echo
log_info "Release URL will be: https://github.com/zkm/cta-linewatch/releases/tag/$NEW_TAG"
