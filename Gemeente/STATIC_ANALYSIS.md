# Static Analysis with PHPStan/Larastan

## 📊 Overview

This project uses **Larastan** (Laravel-specific PHPStan) for static code analysis to detect potential runtime errors, type issues, and improve overall code quality.

## 🚀 Quick Start

### Running Static Analysis
```bash
# Run PHPStan analysis
composer phpstan

# Run all code quality checks (PHPStan + Pint)
composer code-quality

# Generate new baseline (when adding new code with known issues)
composer phpstan-baseline
```

## ⚙️ Configuration

### PHPStan Configuration (`phpstan.neon`)
- **Analysis Level**: Level 6 (out of 9) - good balance of strictness vs practicality
- **Paths Analyzed**: `app/`, `config/`, `database/`, `routes/`, `tests/`
- **Laravel Integration**: Full Laravel framework understanding via Larastan
- **Baseline**: Current issues are baselined to prevent regression

### Key Features Enabled
- ✅ **Laravel-aware analysis** - understands Eloquent, Facades, etc.
- ✅ **Model property checking** - validates model attributes
- ✅ **Type inference** - advanced type checking for Laravel patterns
- ✅ **Baseline support** - track improvements without blocking development

## 📈 Current Status

### Baseline Statistics
- **Total Issues Baselined**: 121 errors
- **Analysis Level**: 6/9
- **Files Analyzed**: 93 files
- **Status**: ✅ No new errors (baseline established)

### Issue Categories
1. **Missing Return Types** (40+ issues)
   - Controllers, models, services need explicit return types
   - Gradual improvement target

2. **Missing Generic Types** (20+ issues)
   - Eloquent relationships need generic type hints
   - Array parameters need value type specification

3. **Model Property Access** (15+ issues)
   - Resource classes accessing dynamic properties
   - Can be resolved with proper type hints

4. **Method Parameters** (25+ issues)
   - Missing parameter type hints
   - Array parameters without value types

5. **Strict Comparisons** (5+ issues)
   - Type mismatches in comparisons
   - Easy fixes for immediate improvement

## 🎯 Improvement Strategy

### Phase 1: Quick Wins (Low Effort, High Impact)
```bash
# Fix strict comparison issues
# Add return type hints to controllers
# Add parameter type hints where obvious
```

### Phase 2: Type Safety (Medium Effort)
```bash
# Add generic types to Eloquent relationships
# Specify array value types in services
# Add PHPDoc blocks for complex methods
```

### Phase 3: Advanced Typing (High Effort)
```bash
# Full generic typing for collections
# Advanced type annotations for Laravel resources
# Custom type definitions for complex data structures
```

## 📋 CI/CD Integration

### GitHub Actions
- **Code Quality Workflow** runs PHPStan on every PR
- **No New Errors Policy** - PRs must not introduce new issues
- **Baseline Protection** - existing issues don't block development

### Pre-commit Hooks (Optional)
```bash
# Add to .git/hooks/pre-commit
composer code-quality
```

## 🛠️ Common Issue Fixes

### Missing Return Types
```php
// Before
public function index()
{
    return view('dashboard');
}

// After  
public function index(): View
{
    return view('dashboard');
}
```

### Array Type Specifications
```php
// Before
public function processData(array $data): array
{
    return $data;
}

// After
/**
 * @param array<string, mixed> $data
 * @return array<string, mixed>
 */
public function processData(array $data): array
{
    return $data;
}
```

### Eloquent Relationship Typing
```php
// Before
public function attachments(): HasMany
{
    return $this->hasMany(Attachment::class);
}

// After
/**
 * @return HasMany<Attachment>
 */
public function attachments(): HasMany
{
    return $this->hasMany(Attachment::class);
}
```

## 📊 Metrics & Goals

### Current Metrics (Baseline)
- **Error Count**: 121 issues
- **Coverage**: 93 files analyzed
- **Level**: 6/9 strictness

### Target Metrics (6 months)
- **Error Reduction**: 50% reduction (60 issues)
- **New Code**: 0 issues in new code
- **Level Increase**: Consider level 7

### Quality Gates
- ✅ **No Regression**: New PRs don't add errors
- ✅ **Gradual Improvement**: Monthly error count reduction
- ✅ **Type Coverage**: Increase explicit typing

## 🔧 IDE Integration

### PHPStorm
- Install PHPStan plugin
- Configure to run on file save
- Enable inline error display

### VS Code
- Install PHPStan extension
- Configure workspace settings
- Enable problems panel integration

## 🎓 Learning Resources

### PHPStan Documentation
- [Official PHPStan Docs](https://phpstan.org/user-guide/getting-started)
- [Larastan Documentation](https://github.com/larastan/larastan)
- [Type System Basics](https://phpstan.org/writing-php-code/phpdocs-basics)

### Best Practices
- Start with lower levels, gradually increase
- Use baseline for existing code
- Focus on new code quality first
- Regular baseline updates

## 📅 Maintenance Schedule

### Weekly
- Review new PHPStan errors in PRs
- Update dependencies if needed

### Monthly  
- Analyze baseline reduction opportunities
- Update PHPStan rules if appropriate
- Team review of type safety improvements

### Quarterly
- Consider increasing analysis level
- Review and update baseline
- Performance impact assessment

---

**Static analysis is an investment in code quality that pays dividends in reduced bugs, better IDE support, and improved developer experience.** 🚀
