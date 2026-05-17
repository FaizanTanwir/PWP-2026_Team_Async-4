<template>
  <div class="card bg-base-100 border border-base-300 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="table table-zebra w-full">
        <thead>
          <tr class="bg-base-200/50">
            <th>Title</th>
            <th>Languages</th>
            <th>Author</th>
            <th>Created</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="course in courses" :key="course.id" class="hover">
            <td>
              <div class="font-bold">{{ course.title }}</div>
              <div class="text-xs opacity-50 font-mono italic">ID: {{ course.id }}</div>
            </td>
            <td>
              <div class="flex items-center gap-2">
                <span class="badge badge-sm uppercase">{{ course.source_language?.code }}</span>
                <ArrowRight class="size-3 opacity-30" />
                <span class="badge badge-sm badge-primary uppercase">{{ course.target_language?.code }}</span>
              </div>
            </td>
            <td>
              <div class="flex items-center gap-3">
                <div class="avatar placeholder">
                  <div class="bg-neutral text-neutral-content rounded-full w-8">
                    <img :src="`https://api.dicebear.com/7.x/avataaars/svg?seed=${course.teacher?.name}`" />

                  </div>
                </div>
                <div>
                  <div class="text-sm font-medium">{{ course.teacher?.name || 'Unknown' }}</div>
                  <div class="opacity-50 tracking-tighter">{{ course.teacher?.email}}</div>
                </div>
              </div>
            </td>
            <td>
              <div class="flex flex-col">
                <span class="text-sm font-semibold">{{ dayjs(course.created_at).format('MMM D, YYYY') }}</span>
                <span class="text-xs opacity-50">{{ dayjs(course.created_at).format('h:mm A') }}</span>
              </div>
            </td>
            <td>
              <div class="flex justify-end gap-1">
                <!-- GET Units -->
                <button @click="goToUnits(course.id)" class="btn btn-square btn-ghost btn-sm" title="View Units">
                  <ExternalLink class="size-4" />
                </button>
                <!-- PUT (Edit) -->
                <button v-if="course.teacher.id === auth.getUser.id" @click="$emit('edit', course)" class="btn btn-square btn-ghost btn-sm text-info" title="Edit Course">
                  <Pencil class="size-4" />
                </button>
                <!-- DELETE -->
                <button v-if="course.teacher.id === auth.getUser.id" @click="confirmDelete(course.id)" class="btn btn-square btn-ghost btn-sm text-error" title="Delete Course">
                  <Trash2 class="size-4" />
                </button>
              </div>
            </td>
          </tr>
          
          <tr v-if="courses.length === 0">
            <td colspan="4" class="text-center py-12 opacity-40 italic">
              No courses found in the database.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- daisyUI Delete Modal -->
    <dialog ref="deleteModal" class="modal modal-bottom sm:modal-middle">
      <div class="modal-box border border-error/20">
        <h3 class="font-bold text-lg text-error">Delete Course?</h3>
        <p class="py-4">This action cannot be undone. All units and sentences under this course will be wiped.</p>
        <div class="modal-action">
          <button @click="closeDeleteModal" class="btn btn-ghost">Cancel</button>
          <button @click="handleDelete" class="btn btn-error" :disabled="isDeleting">
            <span v-if="isDeleting" class="loading loading-spinner loading-xs"></span>
            Confirm Delete
          </button>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop">
        <button>close</button>
      </form>
    </dialog>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { ArrowRight, Pencil, Trash2, ExternalLink } from 'lucide-vue-next'
import dayjs from 'dayjs'
import api from '@/utils/api'
import { useToast } from "vue-toastification";
import { useAuthStore } from '@/stores/auth'

const props = defineProps(['courses'])
const emit = defineEmits(['refresh', 'edit'])
const router = useRouter()
const toast = useToast();
const auth = useAuthStore()

const deleteModal = ref(null)
const selectedId = ref(null)
const isDeleting = ref(false)

const confirmDelete = (id) => {
  selectedId.value = id
  deleteModal.value.showModal()
}

const closeDeleteModal = () => {
  deleteModal.value.close()
  selectedId.value = null
}

const handleDelete = async () => {
  isDeleting.value = true
  try {
    await api.delete(`/courses/${selectedId.value}`)
    emit('refresh')
    toast.success(`Course deleted successfully!`);
    closeDeleteModal()
  } catch (err) {
    console.error(err)
    const errorMessage = err.response?.data?.message || "An unexpected error occurred."
    toast.error(errorMessage);
  } finally {
    isDeleting.value = false
  }
}

const goToUnits = (id) => {
  // Journey Start: Move to the Courses page for this language
  // router.push(`/languages/${id}/courses`)
  router.push({
    name: 'Units',
    params: { id }
  })
}
</script>