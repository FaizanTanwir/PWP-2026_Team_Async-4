import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/utils/axios'

export const useUnitStore = defineStore('units', () => {

  const units = ref([])

  const unitsList = computed(() => units.value || [])

  const getUnitsByCourse = async (courseId) => {
    try {
      const res = await api.get(`/courses/${courseId}/units`)
      units.value = res.data
    } catch (err) {
      console.error('Failed to fetch units', err)
      units.value = []
    }
  }

  const createUnit = async (courseId, payload) => {
    try {
      const res = await api.post(`/courses/${courseId}/units`, payload)
      units.value.push(res.data)
      return res.data
    } catch (err) {
      console.error('Failed to create unit', err)
      throw err
    }
  }

  const updateUnit = async (unitId, payload) => {
    try {
      const res = await api.patch(`/units/${unitId}`, payload)

      const index = units.value.findIndex(u => u.id === unitId)
      if (index !== -1) {
        units.value[index] = res.data
      }

      return res.data
    } catch (err) {
      console.error('Failed to update unit', err)
      throw err
    }
  }

  const deleteUnit = async (unitId) => {
    try {
      await api.delete(`/units/${unitId}`)
      units.value = units.value.filter(u => u.id !== unitId)
    } catch (err) {
      console.error(err)
    }
  }

  return {
    unitsList,
    getUnitsByCourse,
    createUnit,
    updateUnit,
    deleteUnit
  }
})