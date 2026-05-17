<template>
  <div class="card bg-base-100 border border-base-300 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="table table-zebra w-full">
        <thead>
          <tr class="bg-base-200/50">
            <th class="w-16">ID</th>
            <th>Unit Title</th>
            <th>Sentences</th>
            <th>Created</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="unit in units" :key="unit.id" class="hover group">
            <td class="font-mono text-xs opacity-50">#{{ unit.id }}</td>
            <td>
              <div class="font-bold group-hover:text-primary transition-colors cursor-pointer" 
                   @click="router.push(`/units/${unit.id}/sentences`)">
                {{ unit.title }}
              </div>
            </td>
            <td>
              <div class="badge badge-ghost badge-sm font-semibold">
                {{ unit.sentences.length || 0 }} items
              </div>
            </td>
            <td class="text-xs opacity-60">
              {{ dayjs(unit.created_at).format('MMM D, YYYY') }}
            </td>
            <td>
              <div class="flex justify-end gap-1">
                <!-- Manage Sentences -->
                <button @click="router.push(`/units/${unit.id}/sentences`)" 
                        class="btn btn-square btn-ghost btn-sm" title="Manage Sentences">
                  <ListMusic class="size-4" />
                </button>
                <!-- Edit -->
                <button @click="$emit('edit', unit)" class="btn btn-square btn-ghost btn-sm text-info">
                  <Pencil class="size-4" />
                </button>
                <!-- Delete -->
                <button @click="confirmDelete(unit.id)" class="btn btn-square btn-ghost btn-sm text-error">
                  <Trash2 class="size-4" />
                </button>
              </div>
            </td>
          </tr>
          
          <tr v-if="units.length === 0">
            <td colspan="5" class="text-center py-12 opacity-40 italic">
              No units created yet. Click "New Unit" to start.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Delete Confirmation Modal -->
    <dialog ref="deleteModal" class="modal modal-bottom sm:modal-middle">
      <div class="modal-box border border-error/20">
        <h3 class="font-bold text-lg text-error">Delete Unit?</h3>
        <p class="py-4 text-sm opacity-70">This will permanently remove the unit and all associated sentences. This action is irreversible.</p>
        <div class="modal-action">
          <button @click="closeDeleteModal" class="btn btn-ghost">Cancel</button>
          <button @click="handleDelete" class="btn btn-error" :disabled="isDeleting">
            <span v-if="isDeleting" class="loading loading-spinner loading-xs"></span>
            Delete Unit
          </button>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Pencil, Trash2, ListMusic } from 'lucide-vue-next'
import dayjs from 'dayjs'
import api from '@/utils/api'
import { useToast } from "vue-toastification";

const props = defineProps(['units'])
const emit = defineEmits(['refresh', 'edit'])
const router = useRouter()
const toast = useToast();

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
    await api.delete(`/units/${selectedId.value}`)
    toast.success("Unit deleted")
    emit('refresh')
    closeDeleteModal()
  } catch (err) {
    const msg = err.response?.data?.message || "Delete failed"
    toast.error(msg)
  } finally {
    isDeleting.value = false
  }
}
</script>