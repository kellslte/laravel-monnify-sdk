# GitHub Actions Workflows

This repository includes several GitHub Actions workflows for automated testing, version management, and package publishing.

## Workflows

### 1. Tests (`tests.yml`)

Runs the test suite across multiple PHP and Laravel versions.

**Triggers:**
- Push to `main` or `develop` branches
- Pull requests to `main` or `develop` branches

**Test Matrix:**
- PHP: 8.1, 8.2, 8.3
- Laravel: 10.*, 11.*

### 2. Release (`release.yml`)

Automates the release process when a new version tag is created.

**Triggers:**
- Push of a tag matching pattern `v*.*.*` (e.g., `v1.2.3`)

**Actions:**
- Validates `composer.json`
- Updates version in `composer.json` to match tag
- Runs test suite
- Creates GitHub Release with release notes
- Publishes to Packagist (if credentials are configured)

### 3. Version Bump (`version-bump.yml`)

Manually triggers version bumping for patch, minor, or major releases.

**Trigger:**
- Manual workflow dispatch from GitHub Actions UI

**Actions:**
- Reads current version from `composer.json`
- Bumps version based on selected type (patch/minor/major)
- Updates `composer.json`
- Creates a new branch and commit
- Creates a Pull Request for review

**Usage:**
1. Go to Actions tab in GitHub
2. Select "Version Bump" workflow
3. Click "Run workflow"
4. Select version type (patch/minor/major)
5. Run
6. Review and merge the created PR
7. After PR is merged, create a tag to trigger the release workflow

### 4. Create Release (`create-release.yml`)

Manually creates a release with a specific version.

**Trigger:**
- Manual workflow dispatch from GitHub Actions UI

**Actions:**
- Updates version in `composer.json`
- Optionally creates and pushes a git tag (which triggers `release.yml`)
- Creates GitHub Release

**Usage:**
1. Go to Actions tab in GitHub
2. Select "Create Release" workflow
3. Click "Run workflow"
4. Enter version number (e.g., `1.2.3`)
5. Choose whether to create tag (recommended: true)
6. Run

### 5. Packagist Sync (`packagist.yml`)

Syncs the package to Packagist when changes are pushed to main branch.

**Triggers:**
- Push to `main` branch
- Manual workflow dispatch

**Actions:**
- Triggers Packagist package update

## Required Secrets

To enable Packagist publishing, you need to configure the following secrets in your GitHub repository:

1. Go to **Settings** → **Secrets and variables** → **Actions**
2. Click **New repository secret**
3. Add the following secrets:

### `PACKAGIST_USERNAME`
Your Packagist username

### `PACKAGIST_TOKEN`
Your Packagist API token. To generate one:
1. Log in to [Packagist.org](https://packagist.org)
2. Go to your profile → **Show API Token**
3. Copy the token

## Workflow Overview

```
┌─────────────────┐
│   Push/PR       │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Run Tests      │
│  (tests.yml)    │
└─────────────────┘

┌─────────────────┐
│ Manual: Version │
│ Bump            │
│ (version-bump)  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Create PR       │
│ (Review & Merge)│
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Manual: Create  │
│ Tag v*.*.*      │
│ OR use          │
│ create-release  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Release         │
│ (release.yml)   │
└────────┬────────┘
         │
         ├─────────► GitHub Release
         │
         └─────────► Packagist
```

## Quick Start Guide

### Initial Setup

1. **Add Packagist Secrets** (required for publishing):
   - Go to Settings → Secrets and variables → Actions
   - Add `PACKAGIST_USERNAME` (your Packagist username)
   - Add `PACKAGIST_TOKEN` (your Packagist API token)

2. **Set up Packagist Repository**:
   - Create an account on [Packagist.org](https://packagist.org)
   - Submit your package: `https://packagist.org/packages/submit`
   - Enter your GitHub repository URL
   - Packagist will automatically sync when tags are pushed

### Releasing a New Version

**Option 1: Using Version Bump Workflow (Recommended)**
1. Run "Version Bump" workflow with desired version type
2. Review and merge the created PR
3. After merge, create a tag manually: `git tag v1.2.3 && git push origin v1.2.3`
4. The release workflow will automatically trigger

**Option 2: Using Create Release Workflow**
1. Run "Create Release" workflow
2. Enter version number (e.g., `1.2.3`)
3. Enable "Create tag" option
4. The release workflow will automatically trigger after tag is created

**Option 3: Manual Tag Creation**
1. Update version in `composer.json` manually
2. Commit and push changes
3. Create and push tag: `git tag -a v1.2.3 -m "Release v1.2.3" && git push origin v1.2.3`
4. The release workflow will automatically trigger

## Notes

- The version in `composer.json` should match the git tag format (without the `v` prefix)
- Packagist publishing only works if secrets are configured
- Tests run on every push/PR to ensure code quality
- Version bumping creates both a commit and a tag automatically
