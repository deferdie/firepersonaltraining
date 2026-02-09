<script setup>
import { ref, computed, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import FlashMessage from '@/Components/molecules/FlashMessage.vue';
import BackLink from '@/Components/molecules/BackLink.vue';
import {
    LayoutGrid,
    Users,
    UsersRound,
    MessageSquare,
    Library,
    Package,
    UserCog,
    Megaphone,
    CreditCard,
    Smartphone,
    Globe,
    Dumbbell,
    Sparkles,
    LogOut,
    Menu,
    X,
} from 'lucide-vue-next';
import Button from '@/Components/atoms/Button.vue';

const mobileMenuOpen = ref(false);
const flashDismissed = ref(false);
const page = usePage();

const flash = computed(() => {
    const f = page.props.flash;
    if (!f || flashDismissed.value) return null;
    for (const key of ['success', 'error', 'warning', 'info', 'status']) {
        if (f[key]) return { type: key, message: f[key] };
    }
    return null;
});

watch(() => page.url, () => { flashDismissed.value = false; });

const backLinkConfig = computed(() => {
    try {
        const current = route().current();
        const map = {
            'trainer.clients.show': { href: route('trainer.clients.index'), label: 'Back to Clients' },
            'trainer.groups.show': { href: route('trainer.groups.index'), label: 'Back to Groups' },
            'trainer.messages.show': { href: route('trainer.messages.index'), label: 'Back to Messages' },
        };
        return map[current] ?? null;
    } catch {
        return null;
    }
});

const navItems = [
    { path: '/trainer/dashboard', label: 'Dashboard', icon: LayoutGrid, routeName: 'trainer.dashboard' },
    { path: '/trainer/clients', label: 'Clients', icon: Users, routeName: 'trainer.clients.index' },
    { path: '/trainer/groups', label: 'Groups', icon: UsersRound, routeName: 'trainer.groups.index' },
    { path: '/trainer/messages', label: 'Messages', icon: MessageSquare, routeName: 'trainer.messages.index' },
    { path: '/trainer/library', label: 'Library', icon: Library, routeName: 'trainer.library.index' },
    { path: '/trainer/packages', label: 'Packages', icon: Package, routeName: 'trainer.packages.index' },
    { path: '/trainer/team', label: 'Team', icon: UserCog, routeName: 'trainer.team.index' },
    { path: '/trainer/marketing', label: 'Marketing', icon: Megaphone, routeName: 'trainer.marketing.index' },
    { path: '/trainer/billing', label: 'Billing', icon: CreditCard, routeName: 'trainer.billing.index' },
    { path: '/trainer/app', label: 'Mobile App', icon: Smartphone, routeName: 'trainer.app.index' },
    { path: '/trainer/website', label: 'My Website', icon: Globe, routeName: 'trainer.website.index' },
];

const isActive = (item) => {
    try {
        // Try to use route().current() first for exact matches
        if (route().current(item.routeName)) {
            return true;
        }
        
        // For routes with sub-pages, check if current route starts with the base route name
        const currentRoute = route().current();
        if (currentRoute) {
            // For client profile pages, highlight the Clients nav item
            if (item.routeName === 'trainer.clients.index' && currentRoute.startsWith('trainer.clients')) {
                return true;
            }
            // For group profile pages, highlight the Groups nav item
            if (item.routeName === 'trainer.groups.index' && currentRoute.startsWith('trainer.groups')) {
                return true;
            }
            // For library sub-pages, highlight the Library nav item
            if (item.routeName === 'trainer.library.index' && currentRoute.startsWith('trainer.library')) {
                return true;
            }
            // For packages sub-pages, highlight the Packages nav item
            if (item.routeName === 'trainer.packages.index' && currentRoute.startsWith('trainer.packages')) {
                return true;
            }
            // For marketing sub-pages, highlight the Marketing nav item
            if (item.routeName === 'trainer.marketing.index' && currentRoute.startsWith('trainer.marketing')) {
                return true;
            }
            // For website sub-pages, highlight the My Website nav item
            if (item.routeName === 'trainer.website.index' && currentRoute.startsWith('trainer.website')) {
                return true;
            }
            // For messages sub-pages, highlight the Messages nav item
            if (item.routeName === 'trainer.messages.index' && currentRoute.startsWith('trainer.messages')) {
                return true;
            }
        }
        
        // Fallback to pathname checking
        const currentPath = window.location.pathname;
        const itemPath = item.path;
        
        // For client profile pages, highlight the Clients nav item
        if (itemPath === '/trainer/clients' && currentPath.startsWith('/trainer/clients')) {
            return true;
        }
        // For group profile pages, highlight the Groups nav item
        if (itemPath === '/trainer/groups' && currentPath.startsWith('/trainer/groups')) {
            return true;
        }
        // For library sub-pages, highlight the Library nav item
        if (itemPath === '/trainer/library' && currentPath.startsWith('/trainer/library')) {
            return true;
        }
        // For packages sub-pages, highlight the Packages nav item
        if (itemPath === '/trainer/packages' && currentPath.startsWith('/trainer/packages')) {
            return true;
        }
        // For marketing sub-pages, highlight the Marketing nav item
        if (itemPath === '/trainer/marketing' && currentPath.startsWith('/trainer/marketing')) {
            return true;
        }
        // For website sub-pages, highlight the My Website nav item
        if (itemPath === '/trainer/website' && currentPath.startsWith('/trainer/website')) {
            return true;
        }
        if (itemPath === '/trainer/messages' && currentPath.startsWith('/trainer/messages')) {
            return true;
        }
        return currentPath === itemPath;
    } catch (e) {
        // Fallback to pathname checking if route() is not available
        const currentPath = window.location.pathname;
        const itemPath = item.path;
        return currentPath === itemPath || currentPath.startsWith(itemPath + '/');
    }
};

const handleLogout = () => {
    router.post(route('trainer.logout'));
};
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Desktop Sidebar -->
        <aside class="hidden md:fixed md:inset-y-0 md:flex md:w-72 md:flex-col">
            <div class="flex flex-col flex-grow bg-white border-r border-gray-200">
                <!-- Logo Section -->
                <div class="flex items-center gap-3 h-20 px-6 border-b border-gray-200">
                    <div class="bg-gradient-to-br from-gray-800 to-gray-900 p-2.5 rounded-xl shadow-md">
                        <Dumbbell class="size-5 text-white" />
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-xl text-gray-900">TrainerPro</span>
                        <span class="text-xs text-gray-500">Fitness Management</span>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-2">
                    <Link
                        v-for="item in navItems"
                        :key="item.path"
                        :href="route(item.routeName)"
                        :class="[
                            'group flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200',
                            isActive(item)
                                ? 'bg-gray-900 text-white shadow-lg shadow-gray-900/20'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                        ]"
                    >
                        <component :is="item.icon" :class="['size-5 transition-transform duration-200', !isActive(item) && 'group-hover:scale-110']" />
                        <span>{{ item.label }}</span>
                        <div v-if="isActive(item)" class="ml-auto size-1.5 rounded-full bg-white" />
                    </Link>
                </nav>

                <!-- AI Feature Badge -->
                <div class="mx-4 mb-4 p-4 bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 rounded-xl">
                    <div class="flex items-center gap-2 mb-2">
                        <Sparkles class="size-4 text-gray-700" />
                        <span class="text-xs font-semibold text-gray-900">AI-Powered</span>
                    </div>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Smart automation and insights to save you time
                    </p>
                </div>

                <!-- Logout Button -->
                <div class="p-4 border-t border-gray-200">
                    <Button
                        variant="outline"
                        class="w-full justify-start gap-3 h-11 border-gray-300 hover:bg-gray-50 hover:border-gray-400 transition-all"
                        @click="handleLogout"
                    >
                        <LogOut class="size-4" />
                        <span>Logout</span>
                    </Button>
                </div>
            </div>
        </aside>

        <!-- Mobile Header -->
        <div class="md:hidden sticky top-0 z-40 flex items-center gap-4 h-16 px-4 border-b border-gray-200 bg-white shadow-sm">
            <Button
                variant="ghost"
                size="icon"
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="hover:bg-gray-100"
            >
                <X v-if="mobileMenuOpen" class="size-5" />
                <Menu v-else class="size-5" />
            </Button>
            <div class="flex items-center gap-2">
                <div class="bg-gradient-to-br from-gray-800 to-gray-900 p-2 rounded-lg">
                    <Dumbbell class="size-4 text-white" />
                </div>
                <span class="font-bold text-gray-900">TrainerPro</span>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div v-if="mobileMenuOpen" class="md:hidden fixed inset-0 top-16 z-30 bg-white">
            <nav class="flex flex-col p-4 space-y-2">
                <Link
                    v-for="item in navItems"
                    :key="item.path"
                    :href="route(item.routeName)"
                    @click="mobileMenuOpen = false"
                    :class="[
                        'flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all',
                        isActive(item)
                            ? 'bg-gray-900 text-white'
                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                    ]"
                >
                    <component :is="item.icon" class="size-5" />
                    <span>{{ item.label }}</span>
                    <div v-if="isActive(item)" class="ml-auto size-1.5 rounded-full bg-white" />
                </Link>
                <div class="pt-4 border-t border-gray-200 mt-4">
                    <Button
                        variant="outline"
                        class="w-full justify-start gap-3 h-11 border-gray-300"
                        @click="handleLogout"
                    >
                        <LogOut class="size-4" />
                        <span>Logout</span>
                    </Button>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <main class="md:pl-72">
            <div class="p-4 md:p-8">
                <div v-if="flash" class="mb-6">
                    <FlashMessage
                        :type="flash.type"
                        :message="flash.message"
                        @dismiss="flashDismissed = true"
                    />
                </div>
                <div v-if="backLinkConfig" class="mb-6">
                    <BackLink :href="backLinkConfig.href" :label="backLinkConfig.label" />
                </div>
                <slot />
            </div>
        </main>
    </div>
</template>
