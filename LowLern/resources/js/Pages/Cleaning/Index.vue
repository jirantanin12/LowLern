<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

interface Area {
  id: number
  name: string
  description?: string
}

interface CleaningLog {
  id: number
  cleaning_area: Area
  title: string
  status: 'PENDING' | 'IN_PROGRESS' | 'COMPLETED'
  before_image?: string
  after_image?: string
  scheduled_date: string
}

const props = defineProps<{
  areas: Area[]
  logs: CleaningLog[]
}>()

const page = usePage()
const user = page.props.auth.user

// Form สำหรับ Admin เพิ่มโซน
const areaForm = useForm({
  name: '',
  description: ''
})

// Form สำหรับ Admin สั่งงาน
const taskForm = useForm({
  cleaning_area_id: '',
  title: '',
  scheduled_date: new Date().toISOString().substring(0, 10)
})

const submitArea = () => {
  areaForm.post('/cleaning/areas', {
    onSuccess: () => areaForm.reset()
  })
}

const submitTask = () => {
  taskForm.post('/cleaning/tasks', {
    onSuccess: () => taskForm.reset()
  })
}

// อัปโหลดรูปภาพงานบ้าน
const activeLogId = ref<number | null>(null)
const uploadForm = useForm({
  status: 'COMPLETED',
  before_image: null as File | null,
  after_image: null as File | null,
})

const handleFileUpload = (e: Event, field: 'before_image' | 'after_image') => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) {
    uploadForm[field] = target.files[0]
  }
}

const submitUpload = (logId: number) => {
  uploadForm.post(`/cleaning/tasks/${logId}`, {
    onSuccess: () => {
      activeLogId.value = null
      uploadForm.reset()
    }
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-6xl mx-auto space-y-8">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">🧹 รายงานและจัดการงานบ้าน</h1>
      </div>

      <!-- 📌 ส่วนควบคุมสำหรับ Admin เท่านั้น -->
      <div v-if="user.role === 'ADMIN'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- ฟอร์มเพิ่มโซน -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-semibold mb-4">🏠 เพิ่มโซนทำความสะอาด</h2>
          <form @submit.prevent="submitArea" class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1">ชื่อโซน / ห้อง</label>
              <input v-model="areaForm.name" type="text" placeholder="เช่น ห้องนอนใหญ่, ห้องน้ำชั้น 1" required class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:border-gray-700 text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">รายละเอียดเพิ่มเติม</label>
              <input v-model="areaForm.description" type="text" placeholder="เช่น กวาด ถู เช็ดกระจก" class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:border-gray-700 text-sm" />
            </div>
            <button type="submit" :disabled="areaForm.processing" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
              + บันทึกโซน
            </button>
          </form>
        </div>

        <!-- ฟอร์มสั่งงานบ้าน -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-semibold mb-4">📌 มอบหมายงานบ้าน</h2>
          <form @submit.prevent="submitTask" class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1">เลือกโซน</label>
              <select v-model="taskForm.cleaning_area_id" required class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:border-gray-700 text-sm">
                <option value="" disabled>-- เลือกโซน --</option>
                <option v-for="area in props.areas" :key="area.id" :value="area.id">{{ area.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">ชื่องานที่ต้องทำ</label>
              <input v-model="taskForm.title" type="text" placeholder="เช่น ถูพื้นและล้างฟิลเตอร์แอร์" required class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:border-gray-700 text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">กำหนดวันที่ทำ</label>
              <input v-model="taskForm.scheduled_date" type="date" required class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:border-gray-700 text-sm" />
            </div>
            <button type="submit" :disabled="taskForm.processing" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700">
              + สั่งงานบ้าน
            </button>
          </form>
        </div>
      </div>

      <!-- 📌 รายการภารกิจงานบ้าน -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold mb-6">📋 รายการภารกิจงานบ้านทั้งหมด</h2>

        <div v-if="props.logs.length === 0" class="text-center py-8 text-gray-500">
          ยังไม่มีรายการงานบ้านในขณะนี้
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div v-for="log in props.logs" :key="log.id" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 space-y-3 bg-gray-50 dark:bg-gray-900/50">
            <div class="flex justify-between items-start">
              <div>
                <span class="text-xs px-2 py-1 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300 font-medium">
                  {{ log.cleaning_area.name }}
                </span>
                <h3 class="font-bold text-base mt-2">{{ log.title }}</h3>
                <p class="text-xs text-gray-500 mt-1">กำหนดทำ: {{ log.scheduled_date }}</p>
              </div>

              <!-- Status Badge -->
              <span 
                :class="{
                  'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300': log.status === 'PENDING',
                  'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300': log.status === 'IN_PROGRESS',
                  'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300': log.status === 'COMPLETED',
                }"
                class="text-xs font-semibold px-2.5 py-1 rounded-full"
              >
                {{ log.status }}
              </span>
            </div>

            <!-- แสดงรูป Before / After (ถ้ามี) -->
            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-200 dark:border-gray-700">
              <div>
                <p class="text-xs font-medium text-gray-500 mb-1">ภาพ Before:</p>
                <img v-if="log.before_image" :src="`/storage/${log.before_image}`" class="w-full h-28 object-cover rounded-md border" />
                <div v-else class="w-full h-28 bg-gray-200 dark:bg-gray-800 rounded-md flex items-center justify-center text-xs text-gray-400">ไม่มีรูปภาพ</div>
              </div>
              <div>
                <p class="text-xs font-medium text-gray-500 mb-1">ภาพ After:</p>
                <img v-if="log.after_image" :src="`/storage/${log.after_image}`" class="w-full h-28 object-cover rounded-md border" />
                <div v-else class="w-full h-28 bg-gray-200 dark:bg-gray-800 rounded-md flex items-center justify-center text-xs text-gray-400">ไม่มีรูปภาพ</div>
              </div>
            </div>

            <!-- ปุ่มเปิดกล่องอัปโหลดรูป (สำหรับส่งงาน) -->
            <div class="pt-2">
              <button 
                v-if="activeLogId !== log.id" 
                @click="activeLogId = log.id"
                class="w-full py-2 bg-indigo-50 dark:bg-gray-800 hover:bg-indigo-100 text-indigo-600 dark:text-indigo-400 font-medium text-xs rounded-lg transition"
              >
                📸 อัปโหลดรูป / อัปเดตสถานะงาน
              </button>

              <!-- กล่องแนบไฟล์ -->
              <div v-else class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-indigo-200 dark:border-indigo-800 space-y-3 mt-2">
                <div>
                  <label class="block text-xs font-medium mb-1">รูป Before (ก่อนทำ)</label>
                  <input type="file" @change="(e) => handleFileUpload(e, 'before_image')" accept="image/*" class="text-xs w-full" />
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1">รูป After (หลังทำ)</label>
                  <input type="file" @change="(e) => handleFileUpload(e, 'after_image')" accept="image/*" class="text-xs w-full" />
                </div>
                <div class="flex gap-2 pt-1">
                  <button @click="submitUpload(log.id)" :disabled="uploadForm.processing" class="flex-1 py-1.5 bg-emerald-600 text-white font-medium text-xs rounded-md">
                    บันทึกส่งงาน
                  </button>
                  <button @click="activeLogId = null" class="px-3 py-1.5 bg-gray-200 dark:bg-gray-700 text-xs font-medium rounded-md">
                    ยกเลิก
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>