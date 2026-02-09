# Architecture Documentation

## System Overview

The Personal Training Fitness App is a Laravel-based monolith application with Inertia.js and Vue 3, designed to help personal trainers manage clients, create programs, and deliver content through a clean, intuitive interface. The application uses Inertia.js to provide a single-page application (SPA) experience while maintaining server-side routing and rendering.

## Technology Stack

### Backend
- **Framework**: Laravel 12+ (PHP 8.2+)
- **Database**: MySQL/MariaDB
- **ORM**: Eloquent
- **Authentication**: Laravel Breeze (includes Inertia + Vue setup)
- **API Authentication**: Laravel Sanctum (for external API access)
- **Storage**: Laravel Storage (local/S3)
- **Real-time**: Laravel Echo + Pusher/Broadcasting
- **Package Manager**: Composer

### Frontend
- **Framework**: Vue 3 (Composition API)
- **SPA Bridge**: Inertia.js
- **Build Tool**: Vite (Laravel default)
- **Routing**: Inertia.js (handled by Laravel routes)
- **Styling**: Tailwind CSS v4
- **Icons**: Lucide Vue
- **State Management**: Vue Composition API (Pinia for complex global state)
- **Package Manager**: npm/pnpm

### Integrations
- **Payments**: Stripe (planned)
- **Food Tracking**: MyFitnessPal, Cronometer, Lose It!, Nutritionix
- **Fitness**: Google Fit, Apple Watch
- **Email**: Laravel Mail (SMTP/SES)
- **Analytics**: TBD

---

## Project Structure

```
/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Laravel controllers
│   │   │   ├── ClientController.php
│   │   │   ├── WorkoutController.php
│   │   │   ├── ProgramController.php
│   │   │   └── ...
│   │   ├── Middleware/        # Custom middleware
│   │   └── Requests/          # Form request validation
│   ├── Models/                # Eloquent models
│   │   ├── Client.php
│   │   ├── Workout.php
│   │   ├── Program.php
│   │   └── ...
│   ├── Services/              # Business logic services
│   └── ...
├── database/
│   ├── migrations/            # Database migrations
│   ├── seeders/              # Database seeders
│   └── factories/            # Model factories
├── resources/
│   ├── js/
│   │   ├── app.js            # Inertia app setup
│   │   ├── Pages/            # Vue page components (use templates)
│   │   │   ├── Clients/
│   │   │   │   ├── Index.vue
│   │   │   │   └── Show.vue
│   │   │   ├── Library/
│   │   │   │   ├── Index.vue
│   │   │   │   └── MealPlans.vue
│   │   │   ├── Dashboard.vue
│   │   │   └── ...
│   │   ├── Components/       # Reusable Vue components (Atomic Design)
│   │   │   ├── atoms/        # Basic building blocks
│   │   │   │   ├── Button.vue
│   │   │   │   ├── Input.vue
│   │   │   │   ├── Label.vue
│   │   │   │   ├── Avatar.vue
│   │   │   │   ├── Icon.vue
│   │   │   │   ├── Badge.vue
│   │   │   │   └── ...
│   │   │   ├── molecules/    # Simple combinations of atoms
│   │   │   │   ├── SearchForm.vue
│   │   │   │   ├── FormField.vue
│   │   │   │   ├── CardHeader.vue
│   │   │   │   ├── StatusBadge.vue
│   │   │   │   └── ...
│   │   │   ├── organisms/    # Complex UI components
│   │   │   │   ├── ClientCard.vue
│   │   │   │   ├── WorkoutList.vue
│   │   │   │   ├── Navigation.vue
│   │   │   │   ├── DataTable.vue
│   │   │   │   └── ...
│   │   │   ├── templates/    # Page layouts without content
│   │   │   │   ├── DashboardLayout.vue
│   │   │   │   ├── ClientProfileLayout.vue
│   │   │   │   └── ...
│   │   │   └── figma/        # Generated components (DO NOT EDIT)
│   │   ├── Layouts/          # App-level layouts
│   │   │   └── AppLayout.vue
│   │   └── Composables/      # Vue composables (like hooks)
│   │       ├── useClients.js
│   │       └── ...
│   ├── css/
│   │   ├── app.css           # Tailwind imports
│   │   └── theme.css         # Design tokens, base styles
│   └── views/
│       └── app.blade.php     # Inertia root template
├── routes/
│   ├── web.php               # Web routes (Inertia)
│   └── api.php               # API routes (Sanctum)
├── config/                   # Laravel configuration files
├── public/                    # Public assets
├── .env                       # Environment variables
├── .cursorrules              # Cursor AI configuration
├── PROJECT_PLAN.md            # Feature roadmap
├── CONTRIBUTING.md            # Developer guidelines
├── Architecture.md           # This file
├── composer.json              # PHP dependencies
└── package.json               # JavaScript dependencies
```

---

## Architectural Patterns

### Component Architecture

#### Page Components
Located in `/resources/js/Pages/`, these are route-level Vue components that:
- Receive data via Inertia props from Laravel controllers
- Manage page-level state using Vue Composition API
- Compose smaller UI components
- Use Inertia for navigation

```vue
<!-- Example: resources/js/Pages/Clients/Show.vue -->
<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import ClientHeader from '@/Components/ClientHeader.vue'
import ClientNavigation from '@/Components/ClientNavigation.vue'
import ClientContent from '@/Components/ClientContent.vue'

const props = defineProps({
  client: Object,
})

const activeSection = ref('activity')
</script>

<template>
  <Head :title="`${client.name} - Client Profile`" />
  
  <div>
    <ClientHeader :client="client" />
    <ClientNavigation 
      :active="activeSection" 
      @update:active="activeSection = $event" 
    />
    <ClientContent :section="activeSection" :client="client" />
  </div>
</template>
```

#### Presentational Components
Located in `/resources/js/Components/`, these are reusable Vue components that:
- Receive data via props
- Emit events for parent communication
- Are highly reusable
- Have minimal state
- Focus on presentation

```vue
<!-- Example: resources/js/Components/ClientCard.vue -->
<script setup>
import Avatar from './ui/Avatar.vue'

const props = defineProps({
  client: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['select'])

function handleClick() {
  emit('select', props.client.id)
}
</script>

<template>
  <div @click="handleClick">
    <Avatar :initials="client.initials" :color="client.color" />
    <h3>{{ client.name }}</h3>
    <p>{{ client.email }}</p>
  </div>
</template>
```

#### UI Components
Located in `/resources/js/Components/ui/`, these are foundational UI building blocks:
- Button, Card, Modal, Input, etc.
- Based on shadcn/ui pattern
- Styled with Tailwind
- Highly composable

### Atomic Design Principles

We follow **Atomic Design** methodology to ensure maximum component reusability and maintainability. Components are organized into a clear hierarchy from smallest to largest:

```
Atoms → Molecules → Organisms → Templates → Pages
```

#### Component Hierarchy

**1. Atoms** (`/resources/js/Components/atoms/`)
- Basic building blocks that cannot be broken down further
- Single responsibility, no business logic
- Highly reusable across the application
- Examples: `Button.vue`, `Input.vue`, `Label.vue`, `Avatar.vue`, `Icon.vue`, `Badge.vue`

```vue
<!-- Example: resources/js/Components/atoms/Button.vue -->
<script setup>
const props = defineProps({
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

**2. Molecules** (`/resources/js/Components/molecules/`)
- Simple combinations of 2-5 atoms
- Simple functionality, reusable across contexts
- Examples: `SearchForm.vue`, `FormField.vue`, `CardHeader.vue`, `StatusBadge.vue`

```vue
<!-- Example: resources/js/Components/molecules/FormField.vue -->
<script setup>
import Label from '@/Components/atoms/Label.vue'
import Input from '@/Components/atoms/Input.vue'

const props = defineProps({
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

**3. Organisms** (`/resources/js/Components/organisms/`)
- Complex UI components combining molecules and atoms
- Domain-specific logic and complex interactions
- Examples: `ClientCard.vue`, `WorkoutList.vue`, `Navigation.vue`, `DataTable.vue`

```vue
<!-- Example: resources/js/Components/organisms/ClientCard.vue -->
<script setup>
import Avatar from '@/Components/atoms/Avatar.vue'
import Badge from '@/Components/atoms/Badge.vue'
import CardHeader from '@/Components/molecules/CardHeader.vue'

const props = defineProps({
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

**4. Templates** (`/resources/js/Components/templates/`)
- Page layouts without real content
- Define structure and component slots
- Examples: `DashboardLayout.vue`, `ClientProfileLayout.vue`

```vue
<!-- Example: resources/js/Components/templates/ClientProfileLayout.vue -->
<script setup>
import Navigation from '@/Components/organisms/Navigation.vue'

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

**5. Pages** (`/resources/js/Pages/`)
- Specific instances of templates with real data
- Use templates and pass data via props
- Page-specific logic and state management

```vue
<!-- Example: resources/js/Pages/Clients/Show.vue -->
<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import ClientProfileLayout from '@/Components/templates/ClientProfileLayout.vue'
import ClientHeader from '@/Components/organisms/ClientHeader.vue'
import ClientNavigation from '@/Components/organisms/ClientNavigation.vue'

const props = defineProps({
  client: Object,
})

const activeSection = ref('activity')
</script>

<template>
  <Head :title="`${client.name} - Client Profile`" />
  
  <ClientProfileLayout>
    <template #header>
      <ClientHeader :client="client" />
      <ClientNavigation 
        :active="activeSection" 
        @update:active="activeSection = $event" 
      />
    </template>
    
    <template #content>
      <!-- Page-specific content -->
    </template>
  </ClientProfileLayout>
</template>
```

#### Composition Rules

1. **Atoms compose molecules** - Molecules use atoms as building blocks
2. **Molecules compose organisms** - Organisms combine molecules and atoms
3. **Organisms compose templates** - Templates use organisms for layout structure
4. **Templates compose pages** - Pages use templates and fill with real data

#### Guidelines

- **Start with atoms** - Build from the smallest components up
- **Check before creating** - Always check if a component already exists
- **Maximum 3 levels deep** - Atom → Molecule → Organism (avoid deeper nesting)
- **Single responsibility** - Each component should do one thing well
- **Reusability first** - Prefer reusable components over one-off solutions
- **Import paths** - Use `@/Components/atoms/Button`, `@/Components/molecules/SearchForm`, etc.

#### Component Naming Conventions

- **Atoms**: Simple names (`Button`, `Input`, `Avatar`)
- **Molecules**: Descriptive names (`SearchForm`, `FormField`, `CardHeader`)
- **Organisms**: Domain names (`ClientCard`, `WorkoutList`, `Navigation`)
- **Templates**: Layout names (`DashboardLayout`, `ClientProfileLayout`)
- **Pages**: Route names (`Clients/Index`, `Clients/Show`)

### Routing Architecture

We use **Laravel routes + Inertia.js** for navigation:

```php
// routes/web.php
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    
    Route::get('/library', [LibraryController::class, 'index'])->name('library.index');
    Route::get('/library/meal-plans', [MealPlanController::class, 'index'])->name('meal-plans.index');
});

// Example Controller
class ClientController extends Controller
{
    public function show(Client $client)
    {
        return Inertia::render('Clients/Show', [
            'client' => $client->load('programs', 'workouts'),
        ]);
    }
}
```

**Frontend Navigation:**

```vue
<!-- Using Inertia Link component -->
<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <Link href="/clients">Clients</Link>
  <Link :href="`/clients/${clientId}`">View Client</Link>
</template>
```

**Why Inertia.js?**
- SPA-like experience without API layer
- Server-side routing with client-side navigation
- Shared data via props (no API calls needed)
- Built-in form handling and validation
- Automatic CSRF protection
- Works seamlessly with Laravel

---

## State Management Strategy

### Current: Local State
We use Vue 3 Composition API for local component state:

```vue
<script setup>
import { ref, reactive, computed } from 'vue'

// Reactive refs for primitive values
const activeSection = ref('activity')
const isModalOpen = ref(false)
const searchQuery = ref('')

// Reactive objects for complex state
const formData = reactive({
  name: '',
  email: '',
  phone: ''
})

// Computed properties
const filteredClients = computed(() => {
  return clients.value.filter(client => 
    client.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})
</script>
```

**When to use:**
- UI state (modals, tabs, dropdowns)
- Form inputs
- Component-specific state
- Computed values derived from reactive state

### Global State (If Needed)
For shared state across many components, use Pinia:

**Pinia Store Example:**
```js
// resources/js/Stores/clientStore.js
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useClientStore = defineStore('client', () => {
  const clients = ref([])
  const selectedClient = ref(null)
  
  const activeClients = computed(() => {
    return clients.value.filter(c => c.status === 'active')
  })
  
  function setClients(newClients) {
    clients.value = newClients
  }
  
  function selectClient(client) {
    selectedClient.value = client
  }
  
  return {
    clients,
    selectedClient,
    activeClients,
    setClients,
    selectClient
  }
})
```

**Usage in Components:**
```vue
<script setup>
import { useClientStore } from '@/Stores/clientStore'

const clientStore = useClientStore()

// Access state
const clients = clientStore.clients
const activeClients = clientStore.activeClients

// Call actions
clientStore.selectClient(client)
</script>
```

### Server-Side State
Inertia automatically shares data from Laravel:

```php
// Controller shares data via Inertia
return Inertia::render('Clients/Index', [
    'clients' => $clients,
    'filters' => $filters,
]);
```

```vue
<!-- Access shared props -->
<script setup>
const props = defineProps({
  clients: Array,
  filters: Object,
})
</script>
```

**When to consider Pinia:**
- Complex client-side state that needs to persist across pages
- Real-time data subscriptions
- Client-side caching
- Complex computed state

**When to use Inertia shared props:**
- Data that comes from the server
- Page-specific data
- Form data and validation errors

---

## Data Flow

### Current: Mock Data
Data is currently defined in controllers or can be seeded:

```php
// In ClientController.php (mock data)
public function index()
{
    $clients = [
        [
            'id' => 1,
            'name' => 'Sarah Johnson',
            'email' => 'sarah.j@email.com',
            // ... more fields
        ],
        // ... more clients
    ];
    
    return Inertia::render('Clients/Index', [
        'clients' => $clients
    ]);
}
```

### Future: Database Integration

#### Architecture Layers

**Inertia Flow (Web Pages):**
```
┌─────────────────────────────────────┐
│         Vue Components              │
│  (Pages & UI Components)            │
└──────────────┬──────────────────────┘
               │ uses Inertia
┌──────────────▼──────────────────────┐
│         Laravel Controllers         │
│  (Handle requests, business logic)   │
└──────────────┬──────────────────────┘
               │ uses
┌──────────────▼──────────────────────┐
│         Eloquent Models             │
│  (Database queries, relationships)  │
└──────────────┬──────────────────────┘
               │ queries
┌──────────────▼──────────────────────┐
│         MySQL Database              │
└─────────────────────────────────────┘
```

**API Flow (External/Mobile):**
```
┌─────────────────────────────────────┐
│         API Clients                 │
│  (Mobile apps, integrations)        │
└──────────────┬──────────────────────┘
               │ HTTP requests
┌──────────────▼──────────────────────┐
│         Laravel API Routes          │
│  (routes/api.php)                   │
└──────────────┬──────────────────────┘
               │ uses
┌──────────────▼──────────────────────┐
│         Laravel Controllers         │
│  (API responses, validation)         │
└──────────────┬──────────────────────┘
               │ uses
┌──────────────▼──────────────────────┐
│         Eloquent Models             │
│  (Database queries)                  │
└──────────────┬──────────────────────┘
               │ queries
┌──────────────▼──────────────────────┐
│         MySQL Database              │
└─────────────────────────────────────┘
```

#### Example: Controller with Eloquent

```php
// app/Http/Controllers/ClientController.php
namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::query()
            ->where('trainer_id', auth()->id())
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15);
        
        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'filters' => $request->only(['search']),
        ]);
    }
    
    public function show(Client $client)
    {
        // Authorize that trainer owns this client
        $this->authorize('view', $client);
        
        $client->load(['programs', 'workouts', 'progressPhotos']);
        
        return Inertia::render('Clients/Show', [
            'client' => $client,
        ]);
    }
}
```

#### Example: Eloquent Model

```php
// app/Models/Client.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'trainer_id',
        'name',
        'email',
        'phone',
        'status',
    ];
    
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
    
    public function programs(): HasMany
    {
        return $this->hasMany(ClientProgram::class);
    }
    
    public function workouts(): HasMany
    {
        return $this->hasMany(WorkoutCompletion::class);
    }
}
```

#### Usage in Vue Components

```vue
<!-- resources/js/Pages/Clients/Index.vue -->
<script setup>
import { Head } from '@inertiajs/vue3'
import ClientsList from '@/Components/ClientsList.vue'

const props = defineProps({
  clients: Object, // Paginated collection
  filters: Object,
})
</script>

<template>
  <Head title="Clients" />
  
  <div>
    <ClientsList :clients="clients.data" />
    <!-- Pagination handled by Inertia -->
  </div>
</template>
```

#### API Usage (For External Clients)

```php
// routes/api.php
use App\Http\Controllers\Api\ClientController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/clients', [ClientController::class, 'index']);
    Route::get('/clients/{client}', [ClientController::class, 'show']);
});
```

```js
// External API client
import axios from 'axios'

const api = axios.create({
  baseURL: 'https://api.example.com',
  headers: {
    'Authorization': `Bearer ${token}`
  }
})

const clients = await api.get('/clients')
```

---

## Design System

### Theme Architecture

The design system is centralized in `/resources/css/theme.css`:

```css
@theme {
  /* Color Palette */
  --color-primary: #1f2937;
  --color-background: #f8f9fa;
  
  /* Typography */
  --font-sans: 'Inter', system-ui, sans-serif;
  
  /* Spacing */
  --spacing-xs: 0.5rem;
  --spacing-sm: 0.75rem;
  /* ... */
}

/* Base Element Styles */
h1 {
  /* Default h1 styling */
}
```

### Component Styling Pattern

```vue
<!-- Consistent card pattern -->
<template>
  <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
    <!-- Card content -->
  </div>
</template>

<!-- Consistent button pattern -->
<Button variant="default">Primary Action</Button>
<Button variant="outline">Secondary Action</Button>
<Button variant="ghost">Tertiary Action</Button>

<!-- Consistent layout pattern -->
<div class="max-w-7xl mx-auto p-6 space-y-6">
  <!-- Page content -->
</div>
```

### Responsive Design

```vue
<!-- Mobile-first approach -->
<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <!-- Responsive grid -->
  </div>
  
  <!-- Conditional rendering -->
  <div class="hidden md:block">Desktop Only</div>
  <div class="md:hidden">Mobile Only</div>
</template>
```

---

## Authentication Flow

```
┌─────────────┐
│   Landing   │
│     Page    │
└──────┬──────┘
       │
       ▼
┌─────────────┐     Yes      ┌──────────────┐
│ Check Auth  ├─────────────▶│  Dashboard   │
│ (Middleware)│              └──────────────┘
└──────┬──────┘
       │ No
       ▼
┌─────────────┐
│   Login/    │
│   Register  │
└──────┬──────┘
       │
       ▼ (Successful auth)
┌──────────────┐
│  Dashboard   │
└──────────────┘
```

### Implementation with Laravel Breeze

**Laravel Breeze** provides authentication scaffolding with Inertia + Vue:

```php
// routes/web.php
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    
    // Protected routes...
});
```

**Vue Login Component:**

```vue
<!-- resources/js/Pages/Auth/Login.vue -->
<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <Head title="Log in" />
  
  <form @submit.prevent="submit">
    <input v-model="form.email" type="email" required />
    <input v-model="form.password" type="password" required />
    <button type="submit" :disabled="form.processing">
      Log in
    </button>
  </form>
</template>
```

**Accessing Authenticated User:**

```vue
<!-- Access via Inertia shared props -->
<script setup>
const props = defineProps({
  auth: {
    type: Object,
    default: () => ({})
  }
})

// auth.user contains the authenticated user
const userName = props.auth.user?.name
</script>
```

**API Authentication with Sanctum:**

```php
// routes/api.php
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Protected API routes...
});
```

**Sanctum Token Usage:**

```js
// External API client
import axios from 'axios'

// Login and get token
const { data } = await axios.post('/api/login', {
  email: 'user@example.com',
  password: 'password'
})

const token = data.token

// Use token for authenticated requests
const api = axios.create({
  baseURL: '/api',
  headers: {
    'Authorization': `Bearer ${token}`
  }
})
```

**Middleware Protection:**

```php
// app/Http/Middleware/EnsureUserIsTrainer.php
public function handle(Request $request, Closure $next)
{
    if (!auth()->user()->is_trainer) {
        abort(403, 'Only trainers can access this resource.');
    }
    
    return $next($request);
}
```

---

## Database Schema

### Core Tables

Laravel migrations define the database schema. Here are the key migrations:

```php
// database/migrations/xxxx_create_users_table.php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->boolean('is_trainer')->default(false);
    $table->rememberToken();
    $table->timestamps();
});

// database/migrations/xxxx_create_clients_table.php
Schema::create('clients', function (Blueprint $table) {
    $table->id();
    $table->foreignId('trainer_id')->constrained('users')->onDelete('cascade');
    $table->string('name');
    $table->string('email');
    $table->string('phone')->nullable();
    $table->string('status')->default('active');
    $table->timestamps();
    
    $table->index('trainer_id');
});

// database/migrations/xxxx_create_programs_table.php
Schema::create('programs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('trainer_id')->constrained('users')->onDelete('cascade');
    $table->string('name');
    $table->text('description')->nullable();
    $table->integer('weeks')->nullable();
    $table->timestamps();
});

// database/migrations/xxxx_create_workouts_table.php
Schema::create('workouts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('trainer_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('program_id')->nullable()->constrained('programs')->onDelete('set null');
    $table->string('name');
    $table->text('description')->nullable();
    $table->timestamps();
});

// database/migrations/xxxx_create_exercises_table.php
Schema::create('exercises', function (Blueprint $table) {
    $table->id();
    $table->foreignId('workout_id')->constrained('workouts')->onDelete('cascade');
    $table->string('name');
    $table->integer('sets')->nullable();
    $table->string('reps')->nullable();
    $table->integer('rest_seconds')->nullable();
    $table->text('notes')->nullable();
    $table->integer('order_index')->default(0);
    $table->timestamps();
    
    $table->index(['workout_id', 'order_index']);
});

// database/migrations/xxxx_create_client_programs_table.php
Schema::create('client_programs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
    $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
    $table->date('start_date')->nullable();
    $table->string('status')->default('active');
    $table->timestamps();
    
    $table->unique(['client_id', 'program_id']);
});

// database/migrations/xxxx_create_workout_completions_table.php
Schema::create('workout_completions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
    $table->foreignId('workout_id')->constrained('workouts')->onDelete('cascade');
    $table->timestamp('completed_at')->useCurrent();
    $table->integer('duration_minutes')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
    
    $table->index('client_id');
    $table->index('completed_at');
});

// database/migrations/xxxx_create_food_entries_table.php
Schema::create('food_entries', function (Blueprint $table) {
    $table->id();
    $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
    $table->string('meal_type')->nullable();
    $table->string('food_name');
    $table->integer('calories')->nullable();
    $table->decimal('protein', 8, 2)->nullable();
    $table->decimal('carbs', 8, 2)->nullable();
    $table->decimal('fat', 8, 2)->nullable();
    $table->timestamp('logged_at')->useCurrent();
    $table->timestamps();
    
    $table->index(['client_id', 'logged_at']);
});

// database/migrations/xxxx_create_progress_photos_table.php
Schema::create('progress_photos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
    $table->string('photo_url');
    $table->string('angle')->nullable();
    $table->decimal('weight', 5, 2)->nullable();
    $table->text('notes')->nullable();
    $table->timestamp('taken_at')->useCurrent();
    $table->timestamps();
    
    $table->index(['client_id', 'taken_at']);
});

// database/migrations/xxxx_create_trainer_notes_table.php
Schema::create('trainer_notes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('trainer_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
    $table->string('category')->nullable();
    $table->text('content');
    $table->timestamps();
    
    $table->index(['trainer_id', 'client_id']);
});

// database/migrations/xxxx_create_messages_table.php
Schema::create('messages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
    $table->text('content');
    $table->boolean('read')->default(false);
    $table->timestamps();
    
    $table->index(['receiver_id', 'read']);
    $table->index('created_at');
});
```

### Authorization & Access Control

Laravel uses **Policies** and **Gates** instead of Row Level Security:

```php
// app/Policies/ClientPolicy.php
namespace App\Policies;

use App\Models\User;
use App\Models\Client;

class ClientPolicy
{
    public function view(User $user, Client $client)
    {
        return $user->id === $client->trainer_id;
    }
    
    public function update(User $user, Client $client)
    {
        return $user->id === $client->trainer_id;
    }
    
    public function delete(User $user, Client $client)
    {
        return $user->id === $client->trainer_id;
    }
}
```

**Usage in Controllers:**

```php
// In ClientController
public function show(Client $client)
{
    $this->authorize('view', $client);
    // ... rest of method
}
```

**Global Scopes for Automatic Filtering:**

```php
// app/Models/Client.php
use Illuminate\Database\Eloquent\Builder;

protected static function booted()
{
    static::addGlobalScope('trainer', function (Builder $builder) {
        if (auth()->check() && auth()->user()->is_trainer) {
            $builder->where('trainer_id', auth()->id());
        }
    });
}
```

---

## Performance Considerations

### Current Optimizations
1. **Component Memoization**: Use `v-memo` or `computed` for expensive Vue components
2. **Lazy Loading**: Inertia automatically code-splits pages
3. **Image Optimization**: Use Laravel's image intervention or CDN
4. **Eager Loading**: Use Eloquent's `with()` to prevent N+1 queries
5. **Query Caching**: Laravel's query cache for frequently accessed data

### Future Optimizations
1. **Database Indexing**: Add indexes on frequently queried columns
2. **Redis Caching**: Cache expensive queries and computed values
3. **Virtual Scrolling**: For large lists (vue-virtual-scroller)
4. **Debouncing**: For search inputs (use composable)
5. **Pagination**: Laravel's built-in pagination
6. **CDN**: For static assets and images
7. **Queue Jobs**: Offload heavy tasks to background workers
8. **Service Worker**: For offline support (PWA)

### Example: Eloquent Eager Loading

```php
// Prevent N+1 queries
$clients = Client::with(['programs', 'workouts', 'progressPhotos'])
    ->where('trainer_id', auth()->id())
    ->get();

// Cache expensive queries
$clients = Cache::remember('trainer_clients_' . auth()->id(), 3600, function () {
    return Client::with(['programs', 'workouts'])
        ->where('trainer_id', auth()->id())
        ->get();
});
```

### Example: Vue Performance Optimization

```vue
<script setup>
import { computed, ref } from 'vue'

// Computed properties are cached
const filteredClients = computed(() => {
  return clients.value.filter(client => 
    client.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

// Use v-memo for expensive lists
</script>

<template>
  <div v-for="client in filteredClients" v-memo="[client.id, client.status]">
    <!-- Expensive component -->
  </div>
</template>
```

---

## Security

### Current Status
- Laravel Breeze authentication (development)
- Mock data in controllers

### Security Measures

#### Authentication
- Laravel Breeze with session-based authentication
- Laravel Sanctum for API token authentication
- Email/password authentication
- Password reset functionality
- Email verification (optional)
- Two-factor authentication (can be added)

#### Authorization
- Laravel Policies for resource authorization
- Middleware for route protection
- Role-based access control (trainer, client, admin)
- API endpoint protection via Sanctum

#### Data Protection
- HTTPS only (enforced in production)
- Encrypted data at rest (database encryption)
- Secure password hashing (bcrypt by default)
- CSRF protection (built into Laravel)
- XSS prevention (Blade templating escapes by default)
- SQL injection prevention (Eloquent ORM)
- Mass assignment protection (fillable/guarded)

#### Best Practices

**Laravel:**
```php
// ✅ Good - Eloquent ORM prevents SQL injection
$client = Client::find($id);
$clients = Client::where('trainer_id', auth()->id())->get();

// ✅ Good - Form Request Validation
class StoreClientRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients',
        ];
    }
}

// ✅ Good - Authorization checks
public function show(Client $client)
{
    $this->authorize('view', $client);
    // ...
}

// ✅ Good - Mass assignment protection
class Client extends Model
{
    protected $fillable = ['name', 'email', 'phone'];
    // or
    protected $guarded = ['id', 'trainer_id'];
}
```

**Vue/Inertia:**
```vue
<!-- ✅ Good - Inertia handles CSRF automatically -->
<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: '',
})

// CSRF token automatically included
form.post('/clients')
</script>

<!-- ✅ Good - Blade escapes output automatically -->
<!-- In Blade templates, {{ $variable }} is automatically escaped -->
```

---

## Error Handling

### Laravel Error Handling

Laravel provides comprehensive error handling out of the box:

```php
// In Controllers
try {
    $client = Client::findOrFail($id);
    // ...
} catch (ModelNotFoundException $e) {
    abort(404, 'Client not found');
} catch (\Exception $e) {
    Log::error('Error fetching client', [
        'error' => $e->getMessage(),
        'client_id' => $id,
    ]);
    return redirect()->back()->with('error', 'Something went wrong');
}

// Or use Laravel's built-in methods
$client = Client::findOrFail($id); // Automatically throws 404 if not found
```

### Inertia Error Handling

Inertia automatically shares validation errors and flash messages:

```php
// In Controller
return Inertia::render('Clients/Create', [
    'errors' => $validator->errors(), // Automatically shared
]);

// Flash messages
return redirect()->back()->with('success', 'Client created!');
```

```vue
<!-- In Vue Component -->
<script setup>
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()

// Access flash messages
const successMessage = computed(() => page.props.flash?.success)
const errorMessage = computed(() => page.props.flash?.error)

// Access validation errors
const errors = computed(() => page.props.errors)
</script>

<template>
  <!-- Display flash messages -->
  <div v-if="successMessage" class="alert alert-success">
    {{ successMessage }}
  </div>
  
  <!-- Display validation errors -->
  <div v-if="errors.email" class="text-red-600">
    {{ errors.email }}
  </div>
</template>
```

### Global Error Handler

```php
// app/Exceptions/Handler.php
public function render($request, Throwable $exception)
{
    if ($exception instanceof ModelNotFoundException) {
        return Inertia::render('Errors/404');
    }
    
    if ($exception instanceof AuthorizationException) {
        return Inertia::render('Errors/403');
    }
    
    return parent::render($request, $exception);
}
```

### Vue Error Handling

```vue
<script setup>
import { onErrorCaptured } from 'vue'

onErrorCaptured((err, instance, info) => {
  console.error('Vue error:', err, info)
  // Log to error tracking service
  return false // Prevent error from propagating
})
</script>
```

### Error States in Components

```vue
<script setup>
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  clients: Array,
  errors: Object,
})

const hasError = computed(() => Object.keys(props.errors).length > 0)
</script>

<template>
  <Head title="Clients" />
  
  <div v-if="hasError" class="error-message">
    <p>Failed to load clients</p>
    <button @click="$inertia.reload()">Retry</button>
  </div>
  
  <div v-else-if="clients.length === 0" class="empty-state">
    <p>No clients found</p>
  </div>
  
  <ClientsList v-else :clients="clients" />
</template>
```

---

## Testing Strategy

### Backend Tests (PHPUnit)

**Unit Tests:**
- Model methods and relationships
- Service classes
- Utility functions

```php
// tests/Unit/Models/ClientTest.php
namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Client;
use App\Models\User;

class ClientTest extends TestCase
{
    public function test_client_belongs_to_trainer()
    {
        $trainer = User::factory()->create();
        $client = Client::factory()->create(['trainer_id' => $trainer->id]);
        
        $this->assertInstanceOf(User::class, $client->trainer);
        $this->assertEquals($trainer->id, $client->trainer->id);
    }
}
```

**Feature Tests:**
- Controller actions
- API endpoints
- Authentication flows
- Authorization checks

```php
// tests/Feature/ClientControllerTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;

class ClientControllerTest extends TestCase
{
    public function test_trainer_can_view_own_clients()
    {
        $trainer = User::factory()->create();
        $client = Client::factory()->create(['trainer_id' => $trainer->id]);
        
        $response = $this->actingAs($trainer)
            ->get(route('clients.show', $client));
        
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Clients/Show')
                ->has('client')
        );
    }
}
```

### Frontend Tests (Vitest)

**Component Tests:**
- Vue component rendering
- User interactions
- Props and emits

```js
// tests/Components/ClientCard.spec.js
import { mount } from '@vue/test-utils'
import ClientCard from '@/Components/ClientCard.vue'

describe('ClientCard', () => {
  it('displays client information', () => {
    const client = {
      id: 1,
      name: 'John Doe',
      email: 'john@test.com'
    }
    
    const wrapper = mount(ClientCard, {
      props: { client }
    })
    
    expect(wrapper.text()).toContain('John Doe')
    expect(wrapper.text()).toContain('john@test.com')
  })
})
```

**Integration Tests:**
- Inertia page interactions
- Form submissions
- Navigation flows

### E2E Tests (Laravel Dusk or Playwright)

- Critical user journeys
- Cross-browser compatibility
- Full authentication flows

```php
// tests/Browser/ClientManagementTest.php
namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ClientManagementTest extends DuskTestCase
{
    public function test_trainer_can_create_client()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->trainer)
                ->visit('/clients')
                ->click('@create-client-button')
                ->type('name', 'John Doe')
                ->type('email', 'john@example.com')
                ->press('Create')
                ->assertSee('John Doe');
        });
    }
}
```

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage

# Run frontend tests
npm run test
```

---

## Build & Deployment

### Build Process

**Development:**
```bash
# Start Laravel development server
php artisan serve

# In another terminal, start Vite dev server
npm run dev
# or
pnpm dev

# Access at http://localhost:8000
```

**Production Build:**
```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install JavaScript dependencies
npm install
# or
pnpm install

# Build frontend assets
npm run build
# or
pnpm build

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force
```

### Environment Variables
```env
# .env
APP_NAME="Personal Training App"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

BROADCAST_DRIVER=pusher
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Stripe
STRIPE_KEY=your-stripe-public-key
STRIPE_SECRET=your-stripe-secret-key

# Pusher (for real-time)
PUSHER_APP_ID=your-pusher-app-id
PUSHER_APP_KEY=your-pusher-key
PUSHER_APP_SECRET=your-pusher-secret
PUSHER_APP_CLUSTER=mt1

# Storage
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
```

### Deployment Options

1. **Laravel Forge** (Recommended for Laravel)
   - Automated server provisioning
   - One-click deployments
   - SSL certificate management
   - Queue workers management
   - Database backups

2. **VPS/Cloud Server** (DigitalOcean, AWS EC2, Linode)
   - Full control over server
   - Nginx + PHP-FPM setup
   - Supervisor for queue workers
   - Redis for caching/sessions
   - MySQL database

3. **Platform-as-a-Service**
   - **Laravel Vapor** (AWS Lambda)
   - **Heroku** (with buildpacks)
   - **Railway**
   - **Fly.io**

### Deployment Checklist

```bash
# 1. Pull latest code
git pull origin main

# 2. Install dependencies
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# 3. Run migrations
php artisan migrate --force

# 4. Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 5. Restart services
sudo supervisorctl restart laravel-worker:*
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

### Server Requirements

- PHP 8.2 or higher
- Composer
- MySQL 8.0+ or MariaDB 10.3+
- Node.js 18+ and npm/pnpm
- Redis (for caching and queues)
- Nginx or Apache
- Supervisor (for queue workers)

---

## Monitoring & Analytics (Future)

### Application Monitoring
- Error tracking: Sentry
- Performance: Web Vitals
- Analytics: Google Analytics / Plausible

### User Behavior
- Track key actions
- Funnel analysis
- Feature usage

### System Health
- Uptime monitoring
- API response times
- Database performance

---

## Scalability Considerations

### Current Limitations
- Single server deployment
- Basic caching (file-based sessions)
- No queue workers for background jobs
- No optimization for large datasets

### Future Scalability
1. **Horizontal Scaling** - Multiple application servers behind load balancer
2. **Database Optimization** - Indexing, query optimization, read replicas
3. **Caching Layer** - Redis for sessions, cache, and queues
4. **Queue Workers** - Background job processing (Supervisor)
5. **CDN** - For static assets and images
6. **Database Sharding** - If needed for very large datasets
7. **Caching Strategies** - Query caching, page caching, fragment caching
8. **API Rate Limiting** - Protect API endpoints from abuse
9. **Database Connection Pooling** - Optimize database connections
10. **Laravel Horizon** - Monitor and manage Redis queues

---

## Development Workflow

### Local Development Setup

```bash
# 1. Clone repository
git clone <repository-url>
cd fitness-app

# 2. Install PHP dependencies
composer install

# 3. Install JavaScript dependencies
npm install
# or
pnpm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Set up database
# Update .env with database credentials
php artisan migrate

# 7. Seed database (optional)
php artisan db:seed

# 8. Start development servers
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server
npm run dev
# or
pnpm dev

# Access at http://localhost:8000
```

### Development Commands

```bash
# Laravel commands
php artisan serve              # Start Laravel dev server
php artisan migrate            # Run migrations
php artisan migrate:fresh      # Fresh migration (drops all tables)
php artisan db:seed            # Seed database
php artisan make:controller    # Create controller
php artisan make:model          # Create model
php artisan make:migration      # Create migration
php artisan route:list         # List all routes
php artisan tinker             # Interactive shell

# Frontend commands
npm run dev                    # Start Vite dev server
npm run build                  # Build for production
npm run watch                  # Watch for changes

# Queue workers (if using queues)
php artisan queue:work         # Process queued jobs
php artisan queue:listen       # Listen for queued jobs
```

### Code Quality

**PHP:**
- PHPStan or Psalm for static analysis
- Laravel Pint for code formatting (built-in)
- PHPUnit for testing

```bash
# Format PHP code
./vendor/bin/pint

# Run tests
php artisan test
```

**JavaScript/Vue:**
- ESLint for code linting
- Prettier for code formatting (if configured)
- Vitest for Vue component testing

```bash
# Lint JavaScript
npm run lint

# Format JavaScript
npm run format

# Run tests
npm run test
```

### Git Workflow
1. Create feature branch from `main`
2. Develop and test locally
3. Run code quality checks
4. Commit changes with conventional commits
5. Push to remote branch
6. Submit pull request
7. Code review
8. Merge to main
9. Auto-deploy to production (if configured)

### Common Development Tasks

**Creating a New Feature:**
```bash
# 1. Create migration
php artisan make:migration create_example_table

# 2. Create model
php artisan make:model Example

# 3. Create controller
php artisan make:controller ExampleController

# 4. Create policy (if needed)
php artisan make:policy ExamplePolicy

# 5. Add routes in routes/web.php

# 6. Create Vue page component
# resources/js/Pages/Examples/Index.vue

# 7. Test locally
php artisan serve
npm run dev
```

**Database Changes:**
```bash
# Create migration
php artisan make:migration add_column_to_table

# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset
```

---

## Docker Setup

### Docker Compose Configuration

The application uses Docker Compose for local development to provide a consistent environment across the team. This eliminates the need to install PHP, MySQL, and Redis locally.

### Services

The Docker Compose setup includes the following services:

- **nginx**: Web server (Alpine-based)
  - Serves Laravel application via PHP-FPM
  - Handles static assets
  - Exposed on port 8000 (maps to internal port 80)
  - Production-like configuration
  
- **app**: Laravel PHP application container (PHP-FPM)
  - PHP 8.2+ with PHP-FPM
  - Composer for PHP dependency management
  - Node.js and npm/pnpm for frontend assets
  - Volume mount for live code changes
  - Exposes PHP-FPM on port 9000 (internal only)
  
- **mysql**: MySQL 8.0 database
  - Persistent data volume
  - Exposed on port 3306
  
- **redis**: Redis for caching and sessions
  - Alpine-based lightweight image
  - Exposed on port 6379

### Docker Compose Structure

```yaml
# docker-compose.yml structure
services:
  nginx:
    image: nginx:alpine
    ports:
      - "8000:80"
    volumes:
      - .:/var/www/html
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
      - ./docker/nginx/nginx.conf:/etc/nginx/nginx.conf
    depends_on:
      - app
    networks:
      - fitness-app-network

  app:
    build: ./docker/php
    volumes:
      - .:/var/www/html
      - ./docker/php/php.ini:/usr/local/etc/php/php.ini
    expose:
      - "9000"
    environment:
      - DB_HOST=mysql
      - DB_DATABASE=fitness_app
      - REDIS_HOST=redis
    depends_on:
      - mysql
      - redis
    networks:
      - fitness-app-network
  
  mysql:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: fitness_app
      MYSQL_ROOT_PASSWORD: root
      MYSQL_PASSWORD: root
    volumes:
      - mysql_data:/var/lib/mysql
    ports:
      - "3306:3306"
    networks:
      - fitness-app-network
  
  redis:
    image: redis:alpine
    ports:
      - "6379:6379"
    networks:
      - fitness-app-network

volumes:
  mysql_data:

networks:
  fitness-app-network:
    driver: bridge
```

### Benefits

- **Consistent Environment**: Same PHP, MySQL, and Redis versions across all developers
- **Easy Setup**: No need to install and configure local services
- **Isolated Dependencies**: Doesn't interfere with other projects
- **Easy Reset**: Can quickly reset database by removing volumes
- **Production-like**: Closer to production environment

### Request Flow

```
Browser → nginx:80 → PHP-FPM:9000 → Laravel Application
         ↓
    Static files served directly by nginx
```

### Development vs Production

**Development:**
- Uses Docker Compose for local development
- Nginx serves application via PHP-FPM
- Volume mounts for live code reloading
- Exposed ports for direct access (nginx on 8000)
- Development-focused PHP configuration

**Production:**
- Typically deployed to cloud platforms (Laravel Forge, Vapor, etc.)
- Nginx + PHP-FPM setup (same as development)
- Uses production-optimized PHP and nginx configuration
- Managed database services
- CDN for static assets

### Volume Mounts

- Application code: `.:/var/www/html` (live reloading)
- PHP configuration: `./docker/php/php.ini:/usr/local/etc/php/php.ini`
- MySQL data: Persistent volume for database data

### Networking

All services are connected via a Docker network (`fitness-app-network`), allowing them to communicate using service names:
- `app` can connect to `mysql` using hostname `mysql`
- `app` can connect to `redis` using hostname `redis`

### Environment Variables

When using Docker, update `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=fitness_app
DB_USERNAME=root
DB_PASSWORD=root

REDIS_HOST=redis
REDIS_PORT=6379
REDIS_DATABASE=0
```

---

## Migration Path

### Phase 1: Current (Mock Data)
- Laravel backend with Inertia + Vue frontend
- Server-side routing via Laravel
- Mock data in controllers
- Basic authentication (Laravel Breeze)

### Phase 2: Database Integration
- MySQL database setup
- Laravel migrations for schema
- Eloquent models and relationships
- Replace mock data with database queries
- Form validation and error handling

### Phase 3: Add Features
- Real-time messaging (Laravel Echo + Pusher)
- Payment processing (Stripe integration)
- File uploads (Laravel Storage)
- Advanced analytics
- Team collaboration features

### Phase 4: Scale
- Performance optimization (caching, indexing)
- Queue workers for background jobs
- API endpoints for mobile apps (Sanctum)
- Internationalization (Laravel localization)
- Advanced automation

---

## Additional Resources

### Documentation
- [Laravel Documentation](https://laravel.com/docs)
- [Inertia.js Documentation](https://inertiajs.com)
- [Vue 3 Documentation](https://vuejs.org)
- [Laravel Breeze](https://laravel.com/docs/breeze)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [Tailwind CSS v4](https://tailwindcss.com)
- [Vite Documentation](https://vitejs.dev)

### Design References
- Trainerize
- My PT Hub
- TrueCoach

---

_Last Updated: February 9, 2026_
