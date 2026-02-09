<script setup>
import { Head, Link } from '@inertiajs/vue3'

defineProps({
  title: String,
})
</script>

<template>
  <Head :title="title" />
  
  <div class="min-h-screen bg-[#f8f9fa]">
    <nav class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <Link href="/" class="flex items-center">
              <span class="text-xl font-semibold text-gray-900">Personal Training App</span>
            </Link>
          </div>
        </div>
      </div>
    </nav>

    <main>
      <slot />
    </main>
  </div>
</template>
