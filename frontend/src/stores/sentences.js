import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/utils/axios'

export const useSentenceStore = defineStore('sentences', () => {

  const sentences = ref([])

  const sentencesList = computed(() => sentences.value || [])

  // GET
  const getSentencesByUnit = async (unitId) => {
    try {
      const res = await api.get(`/units/${unitId}/sentences`)
      sentences.value = res.data
    } catch (err) {
      console.error(err)
      sentences.value = []
    }
  }

  // CREATE
  const createSentence = async (unitId, payload) => {
    try {
      const res = await api.post(`/units/${unitId}/sentences`, payload)
      sentences.value.push(res.data)
      return res.data
    } catch (err) {
      console.error(err)
      throw err
    }
  }

  // ✏️ UPDATE SENTENCE (NEW)
  const updateSentence = async (sentenceId, payload) => {
    try {
      const res = await api.patch(`/sentences/${sentenceId}`, payload)

      const index = sentences.value.findIndex(s => s.id === sentenceId)
      if (index !== -1) {
        sentences.value[index] = res.data
      }

      return res.data
    } catch (err) {
      console.error('Update sentence failed:', err.response?.data || err)
      throw err
    }
  }

  // 🗑 DELETE SENTENCE (optional but useful)
  const deleteSentence = async (sentenceId) => {
    try {
      await api.delete(`/sentences/${sentenceId}`)
      sentences.value = sentences.value.filter(s => s.id !== sentenceId)
    } catch (err) {
      console.error(err)
    }
  }

  return {
    sentencesList,
    getSentencesByUnit,
    createSentence,
    updateSentence,
    deleteSentence
  }
})