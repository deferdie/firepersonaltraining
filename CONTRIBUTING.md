# Contributing Guide

## Getting Started

### Prerequisites
- Node.js (v18 or higher)
- pnpm package manager
- Git
- Code editor (VS Code with Cursor recommended)

### Initial Setup

#### Option 1: Docker Setup (Recommended)

Docker provides a consistent development environment without needing to install PHP, MySQL, or Redis locally.

```bash
# Clone the repository
git clone <repository-url>
cd fitness-app

# Start all services
docker-compose up -d

# Install PHP dependencies
docker-compose exec app composer install

# Install JavaScript dependencies
docker-compose exec app npm install
# or
docker-compose exec app pnpm install

# Copy environment file
docker-compose exec app cp .env.example .env

# Generate application key
docker-compose exec app php artisan key:generate

# Run migrations
docker-compose exec app php artisan migrate

# Start Vite dev server (in a separate terminal or use docker-compose exec)
docker-compose exec app npm run dev
# or
docker-compose exec app pnpm dev

# Access application at http://localhost:8000
```

**Common Docker Commands:**

```bash
# View logs
docker-compose logs -f nginx
docker-compose logs -f app
docker-compose logs -f mysql
docker-compose logs -f redis

# Execute commands in container
docker-compose exec app php artisan migrate
docker-compose exec app php artisan tinker
docker-compose exec app npm run dev
docker-compose exec app composer require package-name

# Test nginx configuration
docker-compose exec nginx nginx -t

# Reload nginx configuration
docker-compose exec nginx nginx -s reload

# Stop services
docker-compose down

# Stop and remove volumes (clean slate)
docker-compose down -v

# Rebuild containers after Dockerfile changes
docker-compose build --no-cache

# Access MySQL directly
docker-compose exec mysql mysql -u root -p fitness_app

# Access Redis CLI
docker-compose exec redis redis-cli
```

#### Option 2: Local Setup

If you prefer to install dependencies locally:

```bash
# Clone the repository
git clone <repository-url>
cd fitness-app

# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
# or
pnpm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Set up database (update .env with database credentials)
php artisan migrate

# Start development servers
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server
npm run dev
# or
pnpm dev

# Access at http://localhost:8000
```

**Requirements for Local Setup:**
- PHP 8.2 or higher
- Composer
- MySQL 8.0+ or MariaDB 10.3+
- Node.js 18+ and npm/pnpm
- Redis (for caching and sessions)

## Project Structure

```
/resources
  /js
    /Pages              # Vue page components (use templates)
      /Clients
        Index.vue
        Show.vue
      /Library
        Index.vue
        MealPlans.vue
    /Components         # Reusable Vue components (Atomic Design)
      /atoms            # Basic building blocks
        Button.vue
        Input.vue
        Label.vue
        Avatar.vue
        Icon.vue
        Badge.vue
      /molecules        # Simple combinations of atoms
        SearchForm.vue
        FormField.vue
        CardHeader.vue
      /organisms        # Complex UI components
        ClientCard.vue
        WorkoutList.vue
        Navigation.vue
      /templates        # Page layouts without content
        DashboardLayout.vue
        ClientProfileLayout.vue
      /figma           # Generated components (DO NOT EDIT)
    /Layouts           # App-level layouts
      AppLayout.vue
    /Composables       # Vue composables (like hooks)
      useClients.js
    app.js             # Inertia app setup
  /css
    fonts.css          # Font imports only
    theme.css          # Design tokens and base styles
```

## Docker Development

### Docker Compose Services

The application uses Docker Compose with the following services:

- **nginx**: Web server (Alpine-based) - serves the application on port 8000
- **app**: Laravel PHP application container (PHP 8.2 FPM, Composer, Node.js)
- **mysql**: MySQL 8.0 database
- **redis**: Redis for caching and sessions

### Environment Variables

When using Docker, update `.env` file with:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=fitness_app
DB_USERNAME=root
DB_PASSWORD=root

REDIS_HOST=redis
REDIS_PORT=6379
```

### Troubleshooting Docker Issues

**Issue: Port already in use**
```bash
# Check what's using the port
lsof -i :8000
# Or change port in docker-compose.yml
```

**Issue: Permission denied errors**
```bash
# Fix file permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

**Issue: Container won't start**
```bash
# Rebuild containers
docker-compose build --no-cache
docker-compose up -d
```

**Issue: Database connection errors**
```bash
# Ensure MySQL container is running
docker-compose ps mysql

# Check MySQL logs
docker-compose logs mysql

# Wait for MySQL to be ready before running migrations
docker-compose exec app php artisan migrate
```

**Issue: Changes not reflecting**
```bash
# Clear Laravel caches
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear
```

### Development Workflow with Docker

```bash
# Start services
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate

# Seed database
docker-compose exec app php artisan db:seed

# Run tests
docker-compose exec app php artisan test

# Access Tinker
docker-compose exec app php artisan tinker

# View application logs
docker-compose logs -f app

# Stop services
docker-compose down
```

## Coding Standards

### JavaScript/Vue
- Use Vue 3 Composition API with `<script setup>`
- Define props with proper types and validation
- Use composables for reusable logic
- Keep components small and focused

```vue
<!-- ✅ Good -->
<script setup>
const props = defineProps({
  client: {
    type: Object,
    required: true,
    validator: (value) => value.id && value.name
  },
  onSelect: {
    type: Function,
    required: true
  }
})
</script>

<!-- ❌ Bad -->
<script setup>
const props = defineProps({
  client: Object, // No validation
  onSelect: Function
})
</script>
```

### Vue Components
- Use Composition API with `<script setup>`
- Keep components small and focused
- Extract complex logic into composables
- Use proper keys in `v-for` lists

```vue
<!-- ✅ Good - Small, focused component -->
<script setup>
defineProps({
  name: {
    type: String,
    required: true
  }
})
</script>

<template>
  <h2 class="font-semibold">{{ name }}</h2>
</template>

<!-- ❌ Bad - Component doing too much -->
<script setup>
// 500 lines of code...
</script>
```

### Styling with Tailwind

#### Do's ✅
- Use Tailwind utility classes
- Follow the design system colors and spacing
- Use responsive classes where appropriate
- Rely on theme.css for typography defaults

```vue
<!-- ✅ Good -->
<template>
  <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
    <!-- Content -->
  </div>
</template>
```

#### Don'ts ❌
- Don't use font size classes unless specifically requested
- Don't use font weight classes unless specifically requested
- Don't use line-height classes unless specifically requested
- Don't create inline styles unless necessary
- Don't create a tailwind.config.js (we use v4)

```vue
<!-- ❌ Bad - Don't override default typography -->
<h1 class="text-3xl font-bold">Title</h1>

<!-- ✅ Good - Let theme.css handle it -->
<h1>Title</h1>
```

### File Naming
- Use PascalCase for component files: `ClientCard.vue`
- Use kebab-case for utility files: `date-utils.js`
- Use kebab-case for style files: `fonts.css`

### Imports
- Group imports logically (Vue, Inertia, third-party, local)
- Use absolute imports with `@/` alias
- Use relative imports for nearby files

```vue
<script setup>
// ✅ Good
import { ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import Button from '@/Components/atoms/Button.vue'
import ClientCard from '@/Components/organisms/ClientCard.vue'
import { useClients } from '@/Composables/useClients'

// ❌ Bad - Mixed order
import ClientCard from '@/Components/organisms/ClientCard.vue'
import { ref } from 'vue'
import Button from '@/Components/atoms/Button.vue'
</script>
```

## Component Patterns

### Modal Components
```vue
<script setup>
defineProps({
  isOpen: Boolean,
})

const emit = defineEmits(['close'])
</script>

<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 bg-black/50 z-50"
      @click="emit('close')"
    >
      <div
        class="..."
        @click.stop
      >
        <slot />
      </div>
    </div>
  </Teleport>
</template>
```

### Data Fetching (Current: Mock Data)
```vue
<script setup>
// Currently we use mock data from Laravel controllers
const props = defineProps({
  clients: {
    type: Array,
    default: () => []
  }
})

// Future: Use composables for client-side data fetching
import { useClients } from '@/Composables/useClients'
const { clients, isLoading, error } = useClients()
</script>
```

### Form Handling
```vue
<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: '',
})

const submit = () => {
  form.post('/clients', {
    onSuccess: () => {
      form.reset()
    }
  })
}
</script>

<template>
  <form @submit.prevent="submit">
    <input v-model="form.name" />
    <input v-model="form.email" type="email" />
    <button type="submit" :disabled="form.processing">
      Submit
    </button>
  </form>
</template>
```

## Atomic Design Guidelines

We follow **Atomic Design** methodology to ensure maximum component reusability and maintainability. All components are organized into a clear hierarchy:

```
Atoms → Molecules → Organisms → Templates → Pages
```

### Component Levels

#### 1. Atoms (`/resources/js/Components/atoms/`)
**Basic building blocks** that cannot be broken down further.

**Characteristics:**
- Single responsibility
- No business logic
- Highly reusable across the application
- Props for customization only

**Examples:** `Button.vue`, `Input.vue`, `Label.vue`, `Avatar.vue`, `Icon.vue`, `Badge.vue`

```vue
<!-- Example: resources/js/Components/atoms/Button.vue -->
<script setup>
defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'outline', 'ghost'].includes(value)
  },
  disabled: Boolean,
})

const emit = defineEmits(['click'])
</script>

<template>
  <button
    :class="[
      'px-4 py-2 rounded-lg transition-colors',
      variant === 'default' && 'bg-gray-900 text-white hover:bg-gray-800',
      variant === 'outline' && 'border border-gray-200 hover:bg-gray-50',
      variant === 'ghost' && 'hover:bg-gray-100',
      disabled && 'opacity-50 cursor-not-allowed'
    ]"
    :disabled="disabled"
    @click="emit('click')"
  >
    <slot />
  </button>
</template>
```

#### 2. Molecules (`/resources/js/Components/molecules/`)
**Simple combinations** of 2-5 atoms.

**Characteristics:**
- Combine atoms to create simple functionality
- Reusable across different contexts
- May have simple internal state

**Examples:** `SearchForm.vue`, `FormField.vue`, `CardHeader.vue`, `StatusBadge.vue`

```vue
<!-- Example: resources/js/Components/molecules/FormField.vue -->
<script setup>
import Label from '@/Components/atoms/Label.vue'
import Input from '@/Components/atoms/Input.vue'

defineProps({
  label: String,
  id: String,
  error: String,
  required: Boolean,
})
</script>

<template>
  <div class="space-y-2">
    <Label :for="id" :required="required">
      {{ label }}
    </Label>
    <Input :id="id" :class="{ 'border-red-500': error }" />
    <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
  </div>
</template>
```

#### 3. Organisms (`/resources/js/Components/organisms/`)
**Complex UI components** combining molecules and atoms.

**Characteristics:**
- Domain-specific logic
- Complex interactions
- May fetch or manage data
- Composed of multiple molecules and atoms

**Examples:** `ClientCard.vue`, `WorkoutList.vue`, `Navigation.vue`, `DataTable.vue`

```vue
<!-- Example: resources/js/Components/organisms/ClientCard.vue -->
<script setup>
import Avatar from '@/Components/atoms/Avatar.vue'
import Badge from '@/Components/atoms/Badge.vue'
import CardHeader from '@/Components/molecules/CardHeader.vue'

defineProps({
  client: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['select', 'edit', 'delete'])
</script>

<template>
  <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
    <CardHeader>
      <Avatar :initials="client.initials" :color="client.color" />
      <div>
        <h3 class="font-semibold">{{ client.name }}</h3>
        <p class="text-sm text-gray-600">{{ client.email }}</p>
      </div>
      <Badge :variant="client.status">{{ client.status }}</Badge>
    </CardHeader>
    <!-- More content... -->
  </div>
</template>
```

#### 4. Templates (`/resources/js/Components/templates/`)
**Page layouts** without real content.

**Characteristics:**
- Define structure and component slots
- No business logic
- Placeholder content only
- Define layout patterns

**Examples:** `DashboardLayout.vue`, `ClientProfileLayout.vue`

```vue
<!-- Example: resources/js/Components/templates/ClientProfileLayout.vue -->
<script setup>
defineSlots({
  header: Object,
  content: Object,
  sidebar: Object,
})
</script>

<template>
  <div class="max-w-7xl mx-auto p-6 space-y-6">
    <slot name="header" />
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2">
        <slot name="content" />
      </div>
      <div>
        <slot name="sidebar" />
      </div>
    </div>
  </div>
</template>
```

#### 5. Pages (`/resources/js/Pages/`)
**Specific instances** of templates with real data.

**Characteristics:**
- Use templates and fill with real data
- Page-specific logic and state
- Receive data via Inertia props
- Route-level components

**Examples:** `Clients/Index.vue`, `Clients/Show.vue`

```vue
<!-- Example: resources/js/Pages/Clients/Show.vue -->
<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import ClientProfileLayout from '@/Components/templates/ClientProfileLayout.vue'
import ClientHeader from '@/Components/organisms/ClientHeader.vue'

defineProps({
  client: Object,
})

const activeSection = ref('activity')
</script>

<template>
  <Head :title="`${client.name} - Client Profile`" />
  
  <ClientProfileLayout>
    <template #header>
      <ClientHeader :client="client" />
    </template>
    
    <template #content>
      <!-- Page-specific content -->
    </template>
  </ClientProfileLayout>
</template>
```

### Composition Rules

1. **Atoms compose molecules** - Molecules use atoms as building blocks
2. **Molecules compose organisms** - Organisms combine molecules and atoms
3. **Organisms compose templates** - Templates use organisms for layout structure
4. **Templates compose pages** - Pages use templates and fill with real data

**Maximum nesting:** Atom → Molecule → Organism (avoid deeper nesting)

### Component Creation Checklist

Before creating a new component:

1. [ ] **Check if it exists** - Search existing components first
2. [ ] **Determine the level** - Is it an atom, molecule, organism, template, or page?
3. [ ] **Start from atoms** - Build from the smallest components up
4. [ ] **Follow naming conventions** - Use appropriate naming for the level
5. [ ] **Place in correct folder** - Use the atomic design folder structure
6. [ ] **Use proper imports** - Import from `@/Components/atoms/`, `@/Components/molecules/`, etc.
7. [ ] **Keep it focused** - Single responsibility principle
8. [ ] **Make it reusable** - Consider future use cases

### Naming Conventions

- **Atoms**: Simple names (`Button`, `Input`, `Avatar`)
- **Molecules**: Descriptive names (`SearchForm`, `FormField`, `CardHeader`)
- **Organisms**: Domain names (`ClientCard`, `WorkoutList`, `Navigation`)
- **Templates**: Layout names (`DashboardLayout`, `ClientProfileLayout`)
- **Pages**: Route names (`Clients/Index`, `Clients/Show`)

### Import Paths

Always use the `@/` alias for component imports:

```vue
<script setup>
// Atoms
import Button from '@/Components/atoms/Button.vue'
import Input from '@/Components/atoms/Input.vue'

// Molecules
import FormField from '@/Components/molecules/FormField.vue'
import SearchForm from '@/Components/molecules/SearchForm.vue'

// Organisms
import ClientCard from '@/Components/organisms/ClientCard.vue'
import Navigation from '@/Components/organisms/Navigation.vue'

// Templates
import DashboardLayout from '@/Components/templates/DashboardLayout.vue'
</script>
```

### When to Create a New Component

**Create a new atom when:**
- You need a basic UI element that doesn't exist
- The element will be reused across multiple molecules/organisms

**Create a new molecule when:**
- You need to combine 2-5 atoms for a specific purpose
- The combination will be reused in multiple organisms

**Create a new organism when:**
- You need a complex component with domain-specific logic
- The component combines multiple molecules and atoms
- It represents a distinct feature or section

**Create a new template when:**
- You need a reusable page layout pattern
- Multiple pages will share the same structure

**Create a new page when:**
- You need a new route with specific functionality
- The page uses a template and fills it with real data

## Design System

### Colors
```vue
<!-- Backgrounds -->
<div class="bg-[#f8f9fa]"><!-- Page background (light grey) --></div>
<div class="bg-white"><!-- Cards --></div>
<div class="bg-gray-50"><!-- Subtle sections --></div>
<div class="bg-gray-900"><!-- Active states, primary buttons --></div>

<!-- Text -->
<p class="text-gray-900"><!-- Primary text (dark charcoal) --></p>
<p class="text-gray-600"><!-- Secondary text --></p>
<p class="text-muted-foreground"><!-- Muted text --></p>

<!-- Borders -->
<div class="border-gray-200"><!-- Standard borders --></div>

<!-- Status Colors -->
<span class="text-green-600"><!-- Success --></span>
<span class="text-red-600"><!-- Danger/Delete --></span>
<span class="text-blue-600"><!-- Info/Primary --></span>
<span class="text-orange-500"><!-- Warning --></span>
```

### Spacing
- Cards: `p-6` (24px padding)
- Sections: `space-y-6` (24px gap)
- Grid gaps: `gap-4` (16px)
- Element gaps: `gap-2` or `gap-3` (8px or 12px)

### Borders & Shadows
```vue
<!-- Cards -->
<div class="border border-gray-200 rounded-xl shadow-sm">
  <!-- Card content -->
</div>

<!-- Hover effect -->
<div class="hover:shadow-md transition-shadow">
  <!-- Content -->
</div>

<!-- Active/Selected -->
<div class="ring-2 ring-blue-500">
  <!-- Content -->
</div>
```

## Git Workflow

### Branch Naming
- Features: `feature/add-workout-builder`
- Bugs: `fix/client-list-search`
- Refactor: `refactor/client-components`

### Commit Messages
Follow conventional commits:
```
feat: add progress photos section to client profile
fix: correct nutrition calculation in meal planner
refactor: extract form components from client page
docs: update README with setup instructions
style: adjust spacing in client cards
```

### Pull Request Process
1. Create a feature branch from `main`
2. Make your changes
3. Test thoroughly
4. Submit PR with clear description
5. Address review feedback
6. Squash and merge when approved

## Testing

### Component Testing
```js
import { mount } from '@vue/test-utils'
import { describe, it, expect } from 'vitest'
import ClientCard from '@/Components/organisms/ClientCard.vue'

describe('ClientCard', () => {
  it('renders client name', () => {
    const client = { id: 1, name: 'Test Client', email: 'test@example.com' }
    const wrapper = mount(ClientCard, {
      props: { client }
    })
    
    expect(wrapper.text()).toContain('Test Client')
  })
})
```

### Testing Checklist
- [ ] Component renders without errors
- [ ] Props are handled correctly
- [ ] User interactions work as expected
- [ ] Loading states display properly
- [ ] Error states are handled
- [ ] Responsive design works on mobile
- [ ] Accessibility requirements met

## Adding New Features

### Checklist
1. [ ] Review PROJECT_PLAN.md to ensure alignment
2. [ ] Check .cursorrules for relevant patterns
3. [ ] **Check existing components** - Don't duplicate functionality
4. [ ] **Design component hierarchy** - Plan atoms → molecules → organisms → templates → pages
5. [ ] **Start with atoms** - Build from smallest components up
6. [ ] Implement with proper Vue prop validation
7. [ ] Follow atomic design principles
8. [ ] Follow design system guidelines
9. [ ] Add necessary state management (composables if needed)
10. [ ] Test on desktop and mobile
11. [ ] Check accessibility
12. [ ] Update documentation if needed
13. [ ] Submit PR with screenshots

### Example: Adding a New Feature Following Atomic Design

**Feature:** Client search functionality

**Step 1: Check existing components**
- Check if `SearchForm` molecule exists
- Check if `Input` atom exists
- Check if `Button` atom exists

**Step 2: Create/Use atoms**
```vue
<!-- Use existing Input atom -->
<!-- Use existing Button atom -->
```

**Step 3: Create molecule (if needed)**
```vue
<!-- resources/js/Components/molecules/SearchForm.vue -->
<script setup>
import Input from '@/Components/atoms/Input.vue'
import Button from '@/Components/atoms/Button.vue'

const searchQuery = defineModel('modelValue', { type: String })

const emit = defineEmits(['search', 'clear'])
</script>

<template>
  <div class="flex gap-2">
    <Input v-model="searchQuery" placeholder="Search clients..." />
    <Button @click="emit('search')">Search</Button>
    <Button variant="ghost" @click="emit('clear')">Clear</Button>
  </div>
</template>
```

**Step 4: Use in organism**
```vue
<!-- resources/js/Components/organisms/ClientList.vue -->
<script setup>
import SearchForm from '@/Components/molecules/SearchForm.vue'
import ClientCard from '@/Components/organisms/ClientCard.vue'

const searchQuery = ref('')
</script>

<template>
  <div>
    <SearchForm v-model="searchQuery" @search="handleSearch" />
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <ClientCard
        v-for="client in filteredClients"
        :key="client.id"
        :client="client"
      />
    </div>
  </div>
</template>
```

**Step 5: Use in page**
```vue
<!-- resources/js/Pages/Clients/Index.vue -->
<script setup>
import { Head } from '@inertiajs/vue3'
import ClientList from '@/Components/organisms/ClientList.vue'

defineProps({
  clients: Array,
})
</script>

<template>
  <Head title="Clients" />
  <ClientList :clients="clients" />
</template>
```

### Example: Adding a New Page

```vue
<!-- 1. Create page component -->
<!-- resources/js/Pages/Example/Index.vue -->
<script setup>
import { Head } from '@inertiajs/vue3'
</script>

<template>
  <Head title="Example Page" />
  
  <div class="max-w-7xl mx-auto p-6">
    <h1>Example Page</h1>
  </div>
</template>
```

```php
// 2. Add route in Laravel
// routes/web.php
Route::get('/example', [ExampleController::class, 'index'])->name('example.index');

// app/Http/Controllers/ExampleController.php
public function index()
{
    return Inertia::render('Example/Index');
}
```

```vue
<!-- 3. Add navigation link if needed -->
<!-- In sidebar or navigation component -->
<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <Link href="/example">Example</Link>
</template>
```

## Package Management

### Before Installing
1. Check if package already exists in `package.json`
2. Research package compatibility
3. Check for required peer dependencies

### Installation
```bash
# Use pnpm
pnpm add package-name

# For dev dependencies
pnpm add -D package-name

# Specific versions (when required)
pnpm add date-fns
# Note: Forms handled via Inertia.js (built-in)
```

### Common Packages
- **Icons**: lucide-vue-next (already installed)
- **Charts**: vue-chartjs or recharts-vue
- **Animation**: @vueuse/motion or vue-transition
- **Forms**: Inertia form handling (built-in)
- **Date handling**: date-fns

## Debugging

### Common Issues

**Issue: Import not found**
```bash
# Make sure package is installed
pnpm add package-name

# Check import path is correct
import { Component } from 'package-name'
```

**Issue: Styles not applying**
```vue
<!-- Make sure Tailwind classes are spelled correctly -->
<div class="bg-white"><!-- ✅ --></div>
<div class="background-white"><!-- ❌ --></div>
```

**Issue: Component not re-rendering**
```vue
<script setup>
import { ref } from 'vue'

// ✅ Good - Use ref and update immutably
const items = ref([1, 2, 3])
items.value = [...items.value, 4]

// ❌ Bad - Direct mutation
items.value.push(4)
</script>
```

## Accessibility

### Requirements
- Use semantic HTML elements
- Include proper ARIA labels
- Ensure keyboard navigation
- Maintain color contrast (4.5:1 minimum)
- Provide text alternatives for images

### Examples
```vue
<!-- ✅ Good - Semantic HTML -->
<button @click="handleClick">Submit</button>

<!-- ❌ Bad - Non-semantic -->
<div @click="handleClick">Submit</div>

<!-- ✅ Good - ARIA label -->
<button aria-label="Close modal" @click="onClose">
  <X class="size-4" />
</button>

<!-- ✅ Good - Alt text -->
<img :src="photo.url" alt="Client progress photo from Feb 9" />
```

## Performance Best Practices

### Image Optimization
- Use appropriate image formats (WebP when possible)
- Implement lazy loading
- Use proper sizing

### Code Splitting
```vue
<!-- Inertia automatically code-splits pages -->
<!-- No manual lazy loading needed -->
```

### Avoid Unnecessary Renders
```vue
<script setup>
import { computed, vMemo } from 'vue'

// Use computed for derived state
const filteredItems = computed(() => {
  return items.value.filter(item => item.active)
})

// Use v-memo for expensive lists
</script>

<template>
  <div v-for="item in items" v-memo="[item.id, item.status]">
    <!-- Expensive component -->
  </div>
</template>
```

## Documentation

### Component Documentation
```vue
<!--
  ClientCard displays a summary of a client's information
  
  Props:
  - client: Object (required) - Client object with name, email, status, etc.
  - onSelect: Function (required) - Callback when card is clicked
-->
<script setup>
const props = defineProps({
  client: {
    type: Object,
    required: true
  },
  onSelect: {
    type: Function,
    required: true
  }
})
</script>
```

### Inline Comments
```vue
<script setup>
// Only comment non-obvious logic
// ✅ Good - Explains "why"
// Calculate adherence percentage based on completed vs. assigned workouts
const adherence = computed(() => (completed.value / total.value) * 100)

// ❌ Bad - States the obvious
// Set the name variable to client.name
const name = props.client.name
</script>
```

## Getting Help

### Resources
- Check `.cursorrules` for project-specific guidelines
- Review `PROJECT_PLAN.md` for feature context
- Look at existing components for patterns
- Refer to library documentation

### Questions
- Open an issue for bugs
- Start a discussion for feature ideas
- Ask in team chat for quick questions

## Code Review Guidelines

### For Authors
- Keep PRs small and focused
- Write clear PR descriptions
- Include screenshots for UI changes
- Respond to feedback promptly

### For Reviewers
- Be constructive and kind
- Focus on correctness, readability, and maintainability
- Suggest improvements but approve if functional
- Check for design system consistency

## Style Guide Summary

### ✅ Do's
- Use Vue 3 Composition API with proper prop validation
- Follow the design system
- Write small, focused components following atomic design
- Test your changes
- Use semantic HTML
- Follow existing patterns
- Keep code DRY (Don't Repeat Yourself)
- Check if components exist before creating new ones

### ❌ Don'ts
- Don't modify protected files (figma components, package-lock files)
- Don't create inline styles unless necessary
- Don't ignore prop validation
- Don't skip testing
- Don't override typography defaults
- Don't commit commented-out code
- Don't push directly to main
- Don't create components that duplicate existing functionality

---

Thank you for contributing to the Personal Training Fitness App! 🎉
