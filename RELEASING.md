# How to Create Your First Stable Release

## The Problem

If you see **"dev-main"** on Packagist, it means Packagist is showing your default branch (main) as the only available version. Users will need to install it with:
```bash
composer require scwar/laravel-monnify-sdk:dev-main
```

This is not ideal because:
- It's not a stable version
- Users prefer semantic versioning (0.1.0, 1.0.0, etc.)
- It may not be clear which version they're using

## The Solution

Create a **version tag** and push it. Packagist will automatically detect it and show it as a stable release.

## Quick Fix - Create Your First Release

Since your `composer.json` already has version `0.1.0`, here's how to create the tag:

### Option 1: Using GitHub UI (Easiest)

1. Go to your repository on GitHub
2. Click **"Releases"** → **"Create a new release"**
3. Click **"Choose a tag"** → Type: `v0.1.0` → Click **"Create new tag: v0.1.0"**
4. Title: `Release v0.1.0`
5. Description: Add release notes (or leave default)
6. Click **"Publish release"**

This will:
- Create the tag `v0.1.0`
- Trigger your release workflow
- Packagist will automatically update

### Option 2: Using Git Commands

```bash
# Make sure you're on main and up to date
git checkout main
git pull origin main

# Create and push the tag
git tag -a v0.1.0 -m "Release v0.1.0"
git push origin v0.1.0
```

### Option 3: Using GitHub Actions Workflow

1. Go to **Actions** tab in GitHub
2. Select **"Create Release"** workflow
3. Click **"Run workflow"**
4. Enter version: `0.1.0`
5. Check **"Create tag"** (enabled by default)
6. Click **"Run workflow"**

This will automatically:
- Update `composer.json` version (if needed)
- Create the tag
- Trigger the release workflow
- Publish to Packagist

## Verify It Worked

After creating the tag, wait a few minutes, then:

1. Check Packagist: https://packagist.org/packages/scwar/laravel-monnify-sdk
2. You should see:
   - `0.1.0` (stable) instead of just `dev-main`
   - Installation command: `composer require scwar/laravel-monnify-sdk`

## Future Releases

For future releases, use one of these methods:

1. **Version Bump Workflow** - Automatically bumps version and creates PR
2. **Create Release Workflow** - Manually specify version
3. **GitHub Releases UI** - Create release through GitHub interface
4. **Manual git tag** - Tag and push manually

See `.github/workflows/README.md` for detailed instructions.

## Important Notes

- Version tags should match `composer.json` version (with `v` prefix)
- Example: If `composer.json` has `"version": "0.1.0"`, create tag `v0.1.0`
- Packagist auto-updates within 5-10 minutes after tag is pushed
- Make sure Packagist secrets are configured for automatic publishing
