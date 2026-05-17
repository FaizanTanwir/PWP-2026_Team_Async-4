<template>
  <div class="card bg-base-100 border border-base-300 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="table table-zebra w-full">
        <thead>
          <tr class="bg-base-200/50">
            <th>Sentence Pair</th>
            <th>Word Tokens Breakdowns</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in sentences" :key="item.id" class="hover">
            <td class="max-w-xs space-y-1">
              <!-- Target Language Phrase -->
              <div class="font-bold text-base text-primary tracking-wide">{{ item.text_target }}</div>
              <!-- Source Language Translation -->
              <div class="text-xs opacity-60 font-medium italic">{{ item.text_source }}</div>
            </td>
            
            <td>
              <!-- Interactive Word Tokens Grouping -->
              <div class="flex flex-wrap gap-1.5 max-w-sm">
                <span 
                  v-for="word in item.words" 
                  :key="word.id" 
                  class="badge badge-neutral text-xs font-mono py-2 cursor-help"
                  :title="`Translation: ${word.translation || 'None'} | Lemma: ${word.lemma || 'N/A'}`"
                >
                  {{ word.term }}
                </span>
              </div>
            </td>

            <td>
              <div class="flex justify-end gap-1">
                <button @click="$emit('edit', item)" class="btn btn-square btn-ghost btn-sm text-info" title="Modify Structure">
                  <Pencil class="size-4" />
                </button>
                <button @click="confirmDelete(item.id)" class="btn btn-square btn-ghost btn-sm text-error" title="Drop Sentence">
                  <Trash2 class="size-4" />
                </button>
              </div>
            </td>
          </tr>

          <tr v-if="sentences.length === 0">
            <td colspan="3" class="text-center py-16 opacity-40 italic">
              No sentences populated inside this unit container yet.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Global standard deletion flow modal -->
    <dialog ref="deleteModal" class="modal modal-bottom sm:modal-middle">
      <div class="modal-box border border-error/20">
        <h3 class="font-bold text-lg text-error">Delete Sentence?</h3>
        <p class="py-3 text-sm opacity-70">This breaks the relational pivot connections to all underlying word tokens.</p>
        <div class="modal-action">
          <button @click="closeDeleteModal" class="btn btn-ghost">Cancel</button>
          <button @click="handleDelete" class="btn btn-error" :disabled="isDeleting">
            <span v-if="isDeleting" class="loading loading-spinner loading-xs"></span>
            Purge Record
          </button>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Pencil, Trash2 } from 'lucide-vue-next'
import api from '@/utils/api'
import { useToast } from "vue-toastification";

const props = defineProps(['sentences'])
const emit = defineEmits(['refresh', 'edit'])

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
    await api.delete(`/sentences/${selectedId.value}`)
    toast.success("Sentence structural layout removed successfully.")
    emit('refresh')
    closeDeleteModal()
  } catch (err) {
    toast.error(err.response?.data?.message || "Purge execution failed.")
  } finally {
    isDeleting.value = false
  }
}
</script>