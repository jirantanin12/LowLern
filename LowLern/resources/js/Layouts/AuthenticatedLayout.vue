<script setup lang="ts">
import { ref } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

const page = usePage()
const user = ref(page.props.auth?.user)

const logout = () => {
  router.post('/logout')
}
</script>

<template>
  <div class="min-h-screen flex bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
    <!-- 📌 Sidebar ด้านซ้าย -->
    <aside class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col justify-between p-4">
      <div>
        <!-- Logo / Title -->
        <div class="text-2xl font-bold tracking-wider text-indigo-600 dark:text-indigo-400 mb-8 px-2">
          LowLern 🏠
        </div>

        <!-- Navigation Links (ใช้ URL ตรง ไม่ผ่าน Ziggy route) -->
        <nav class="space-y-2">
          <Link
            href="/dashboard"
            class="flex items-center gap-3 px-3 py-2 rounded-lg transition hover:bg-indigo-50 dark:hover:bg-gray-700"
          >
            📊 <span>Dashboard</span>
          </Link>

          <Link
            href="/calendar"
            class="flex items-center gap-3 px-3 py-2 rounded-lg transition hover:bg-indigo-50 dark:hover:bg-gray-700"
          >
            📅 <span>Calendar</span>
          </Link>

          <Link
            href="/cleaning"
            class="flex items-center gap-3 px-3 py-2 rounded-lg transition hover:bg-indigo-50 dark:hover:bg-gray-700"
          >
            🧹 <span>House Cleaning</span>
          </Link>

          <Link
            href="/academic"
            class="flex items-center gap-3 px-3 py-2 rounded-lg transition hover:bg-indigo-50 dark:hover:bg-gray-700"
          >
            📚 <span>Academic Tracker</span>
          </Link>
        </nav>
      </div>

      <!-- User Profile & Logout (ด้านล่างสุดของ Sidebar) -->
      <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <div class="px-2 mb-2">
          <p class="font-semibold text-sm">{{ user?.name }}</p>
          <span class="text-xs px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 font-mono">
            {{ user?.role }}
          </span>
        </div>
        <button
          @click="logout"
          class="w-full text-left px-3 py-2 rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-gray-700 transition"
        >
          🚪 ออกจากระบบ
        </button>
      </div>
    </aside>

    <!-- 📌 Content Area ด้านขวา -->
    <main class="flex-1 p-8 overflow-y-auto">
      <slot />
    </main>
  </div>
</template>