# Design System — Laradmin

## Overview

Laravel admin kit. Blade + Alpine.js + Tailwind CSS. All UI via Tailwind utilities. No custom CSS.

Components: Blade anonymous components in `resources/views/components/`.

Philosophy: functional, minimal, consistent. Clarity > decoration.

## Layout

### Containers

| Container    | Max width     | Usage                           |
|--------------|---------------|---------------------------------|
| Page content | `max-w-7xl`   | Dashboard, CRUD pages           |
| Card inner   | `max-w-xl`    | Profile forms, settings panels  |
| Guest card   | `sm:max-w-md` | Login, register, password reset |

### Spacing

Tailwind default scale. Key patterns:

- Page vertical: `py-12`
- Card: `p-4 sm:p-8` or `p-6`
- Sections: `space-y-6`
- Container horizontal: `px-4 sm:px-6 lg:px-8`
- Form fields: `mt-4` between groups, `mt-1` label→input, `mt-2` input→error

### Flex Patterns

- Top nav: `flex justify-between h-16`
- Nav links: `flex` + `space-x-8`
- Form actions: `flex items-center justify-end`
- Guest layout: `flex flex-col sm:justify-center items-center`

## Colors

Tailwind defaults. No custom palette. Documenting **which tokens used where**, not values.

### Gray (primary)

| Token      | Usage                       |
|------------|-----------------------------|
| `gray-100` | Page background             |
| `gray-200` | Borders, dividers           |
| `gray-300` | Input borders               |
| `gray-500` | Muted text, placeholder     |
| `gray-600` | Secondary text              |
| `gray-700` | Labels, hover states        |
| `gray-800` | Headings, primary button bg |
| `gray-900` | Body text                   |

### Monochrome palette — gray only. No accent color.

All interactive states (focus rings, active borders, nav states) use gray tokens from the table above. Success messages use `text-gray-700`. Feature card icons use `bg-gray-100` / `text-gray-700`.

### Red (destructive)

| Token     | Usage                    |
|-----------|--------------------------|
| `red-500` | Danger hover, focus ring |
| `red-600` | Danger button bg         |
| `red-700` | Danger button active     |

## Typography

### Font

Custom: **Figtree** via Bunny Fonts (`figtree:400,500,600`).

```js
// tailwind.config.js
fontFamily: { sans: ['Figtree', ...defaultTheme.fontFamily.sans] }
```

### Scale

| Element             | Classes                                                         |
|---------------------|-----------------------------------------------------------------|
| Page heading        | `font-semibold text-xl text-gray-800 leading-tight`             |
| Section heading     | `text-lg font-medium text-gray-900`                             |
| Nav user name       | `font-medium text-base text-gray-800`                           |
| Body text           | `text-gray-900`                                                 |
| Section description | `mt-1 text-sm text-gray-600`                                    |
| Labels              | `font-medium text-sm text-gray-700`                             |
| Links/buttons       | `text-xs uppercase tracking-widest font-semibold`               |
| Small text          | `text-sm text-gray-600`                                         |
| Error messages      | `text-sm text-red-600`                                          |
| Success messages    | `text-sm text-gray-700` / `font-medium text-sm text-gray-700` |

## Component Inventory

All in `resources/views/components/`.

### Buttons

| Component              | Variant     | Key Classes                                                                                |
|------------------------|-------------|--------------------------------------------------------------------------------------------|
| `<x-primary-button>`   | Submit      | `bg-gray-800 text-white hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:ring-gray-700 rounded-md` |
| `<x-secondary-button>` | Cancel      | `bg-white border-gray-300 text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-gray-700 rounded-md` |
| `<x-danger-button>`    | Destructive | `bg-red-600 text-white hover:bg-red-500 active:bg-red-700 rounded-md`                      |

Common: `inline-flex items-center px-4 py-2 font-semibold text-xs uppercase tracking-widest focus:ring-2 focus:ring-offset-2 transition duration-150`.

### Form Controls

| Component         | Props      | Notes                                                                                |
|-------------------|------------|--------------------------------------------------------------------------------------|
| `<x-text-input>`  | `disabled` | `border-gray-300 focus:border-gray-700 focus:ring-gray-700 rounded-md shadow-sm` |
| `<x-input-label>` | `value`    | `block font-medium text-sm text-gray-700`                                            |
| `<x-input-error>` | `messages` | `text-sm text-red-600 space-y-1`, renders `<ul><li>` per message                     |

Checkboxes: raw `<input type="checkbox">` with `rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700`.

### Layout

| Component                | Purpose                                                     |
|--------------------------|-------------------------------------------------------------|
| `<x-app-layout>`         | Authenticated — nav + optional header + `<main>` slot       |
| `<x-guest-layout>`       | Unauthenticated — centered logo + card slot                 |
| `<x-slot name="header">` | Page heading inside `<header class="bg-white shadow">`      |

### Navigation

| Component                 | Notes                                   |
|---------------------------|-----------------------------------------|
| `<x-nav-link>`            | Desktop, `:active` prop for state       |
| `<x-responsive-nav-link>` | Mobile hamburger menu                   |
| `<x-dropdown>`            | `align` (left/right/top), `width` (48)  |
| `<x-dropdown-link>`       | Link inside dropdown                    |

### Overlays

| Component              | Props                               | Notes                       |
|------------------------|-------------------------------------|-----------------------------|
| `<x-modal>`            | `name`, `show`, `maxWidth`          | Alpine.js, focus trap, ESC  |
| `<x-application-logo>` | SVG via `$attributes`               | Laravel logo                |

### Status

| Component                 | Props    | Notes                |
|---------------------------|----------|----------------------|
| `<x-auth-session-status>` | `status` | Flash session message|

## Page Patterns

### Authenticated Page

```
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Page Title
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- content -->
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

### Guest Page

```
<x-guest-layout>
    <form method="POST" action="{{ route('...') }}">
        @csrf
        <!-- fields -->
        <div class="flex items-center justify-end mt-4">
            <x-primary-button>Action</x-primary-button>
        </div>
    </form>
</x-guest-layout>
```

### Card Section (Profile-style)

```
<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
    <div class="max-w-xl">
        <!-- form partial -->
    </div>
</div>
```

## Responsive

Tailwind default breakpoints. Key patterns:

- Nav: `hidden sm:flex` desktop, `sm:hidden` hamburger
- Guest card: full-width mobile, centered `sm:+`
- Cards: `sm:rounded-lg` (square mobile, rounded desktop)

## Motion

All via Alpine.js `x-transition`:

| Element        | Enter                                           | Leave           |
|----------------|-------------------------------------------------|-----------------|
| Dropdown       | `ease-out 200ms` (opacity + scale)              | `ease-in 75ms`  |
| Modal backdrop | `ease-out 300ms` (opacity)                      | `ease-in 200ms` |
| Modal panel    | `ease-out 300ms` (opacity + translateY + scale) | `ease-in 200ms` |
| Buttons        | `transition ease-in-out duration-150`           | —               |

## Asset Pipeline

- **Bundler**: Vite 8
- **Entry**: `resources/css/app.css`, `resources/js/app.js`
- **Font**: Bunny Fonts `figtree:400,500,600`
- **Plugins**: `@tailwindcss/forms`, `@tailwindcss/vite`, `laravel-vite-plugin`
- **JS**: Alpine.js via `resources/js/bootstrap.js`

## Conventions

1. New component → `resources/views/components/<name>.blade.php`
2. Use `$attributes->merge()` for class override support
3. Props via `@props([...])`
4. Palette: gray monochrome + red destructive
5. Card pattern: `sm:rounded-lg shadow-sm`
6. Interactive states: Tailwind transitions
7. Mobile-first, `sm:` breakpoint minimum