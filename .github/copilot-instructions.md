<!-- Use this file to provide workspace-specific custom instructions to Copilot. For more details, visit https://code.visualstudio.com/docs/copilot/copilot-customization#_use-a-githubcopilotinstructionsmd-file -->

# Copilot Instructions for Sistem Informasi Desa Digital

This is a Laravel-based Digital Village Information System project for Kabupaten Katingan.

## Project Context

- **Purpose**: Village digitalization system for 150+ villages
- **Users**: Village administrators (Kepala Desa, Sekretaris, Kaur) and residents
- **Technology**: Laravel 10 with PHP 8.1+, MySQL, Bootstrap 5

## Code Standards

- Follow Laravel coding conventions and PSR-12 standards
- Use descriptive variable and method names in Indonesian/English mix
- Implement proper validation for all forms
- Use Laravel's built-in security features (CSRF, authentication, authorization)
- Write clean, well-documented code with proper comments

## Key Features to Maintain

1. **Multi-role Authentication**: Three user roles with different permissions
2. **Data Security**: Protect sensitive resident data
3. **User Experience**: Simple, intuitive interface for village administrators
4. **Performance**: Optimize for low-bandwidth environments

## Database Naming Conventions

- Use snake_case for table and column names
- Use Indonesian terms where appropriate (e.g., 'penduduk' for residents)
- Maintain referential integrity with proper foreign keys

## UI/UX Guidelines

- Mobile-responsive design
- Accessible interface for all user types
- Clear navigation and breadcrumbs
- Consistent styling with Bootstrap 5 components
