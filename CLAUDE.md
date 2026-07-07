# CLAUDE.md — CVactive Laravel Project

## Project Context

CVactive is a Laravel job board platform with:
- Admin dashboard (Blade templates, Livewire-style controllers)
- HR job post management with AI CV scoring
- Blog, FAQ, Plan/Pricing, Contact management
- Template gallery for CV resumes
- Multi-language support (Vietnamese primary)

## Skill routing

When the user's request matches an available skill, invoke it via the Skill tool. When in doubt, invoke the skill.

Key routing rules:
- Bugs/errors → invoke /investigate
- QA/testing site behavior → invoke /qa or /qa-only
- Code review/diff check → invoke /review
- Ship/deploy/PR → invoke /ship or /land-and-deploy
- Architecture → invoke /plan-eng-review
- Design system/plan review → invoke /design-consultation or /plan-design-review
- Full review pipeline → invoke /autoplan
- Save progress → invoke /context-save
- Resume context → invoke /context-restore

## Stack

- Laravel 10+ (PHP 8.x)
- Blade templates + Vite
- SQLite/PostgreSQL
- AI CV Scoring (custom service layer)
- File storage: public/storage
