---
name: ui-page
description: Scaffold a complete Blade page with the ArcheryStats layout and design system
user-invocable: true
argument-hint: "[page-name]"
---

# Scaffold a Page

Generate a complete Blade page that extends the ArcheryStats layout with professional styling.

## Page Template Structure

```blade
@extends('layouts.app')
@section('title', 'Page Title')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="page-heading text-slate-900 font-bold">Page Title</h1>
            <p class="text-sm text-gray-500 mt-1">Description text</p>
        </div>
        <div class="mt-4 sm:mt-0 flex gap-3">
            {{-- Action buttons here --}}
        </div>
    </div>

    {{-- Content --}}

</div>
@endsection
```

## Design System (Quick Reference)

| Element | Classes |
|---------|---------|
| Card | `bg-white rounded-2xl shadow-sm border border-gray-100 p-6` |
| Stat card | `bg-white rounded-2xl shadow-sm border border-gray-100 stat-card p-6` |
| Primary btn | `btn-primary px-6 py-2.5 rounded-xl text-sm` |
| Navy btn | `btn-navy px-6 py-2.5 rounded-xl text-sm` |
| Input | `w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400` |
| Section head | `section-header text-sm text-slate-700` |
| Table | `min-w-full divide-y divide-gray-200` in a `overflow-x-auto` wrapper |
| Badge | `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold` |
| Page bg | `#f1f5f9` (already set by layout) |

## Theme Colors (CSS Variables)
- `var(--th-primary)` — primary color (buttons, accents)
- `var(--th-primary-hover)` — primary hover state
- `var(--th-sidebar)` — sidebar/dark backgrounds
- `var(--th-sidebar-hover)` — sidebar hover state
- `var(--th-accent)` — nav highlights, badges
- Never hardcode theme colors — always use CSS variables

## Available Libraries
- **Tailwind CSS** (CDN) — utility classes
- **Alpine.js** (CDN) — interactivity
- **Preline UI** (CDN) — advanced components (modals, tooltips, tabs, dropdowns, accordions)
- **Heroicons** — icons (inline SVG)

## Steps

1. Ask what the page is for if not specified
2. Read existing similar pages for consistency (e.g., `resources/views/archers/index.blade.php`)
3. Generate the full Blade view extending `layouts.app`
4. Include responsive design (mobile-first)
5. Add Alpine.js / Preline UI for interactivity (modals, filters, toggles)
6. Use CSS variables for theme colors
7. Include empty states and loading indicators where appropriate
8. Create the route in `routes/web.php` if needed
9. Create the controller method if needed
