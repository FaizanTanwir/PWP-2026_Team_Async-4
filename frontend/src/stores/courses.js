import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/utils/axios'

export const useCourseStore = defineStore('course', () => {

  const courses = ref([])

  const coursesList = computed(() => courses.value || [])

  const getCoursesByLanguage = async (languageId) => {
    try {
      const res = await api.get(`/languages/${languageId}/courses`)
      courses.value = res.data
    } catch (err) {
      console.error('Failed to fetch courses', err)
      courses.value = []
    }
  }

  const getCourseById = async (courseId) => {
    try {
      const res = await api.get(`/courses/${courseId}`)
      return res.data
    } catch (err) {
      console.error('Failed to fetch course', err)
      return null
    }
  }

  const createCourse = async (payload, languageId) => {
    try {
      const res = await api.post(`/languages/${languageId}/courses`, payload)
      courses.value.push(res.data)
      return res.data
    } catch (err) {
      console.error('Failed to create course', err)
      throw err
    }
  }

  const updateCourse = async (courseId, payload) => {
    try {
      const res = await api.patch(`/courses/${courseId}`, payload)

      const index = courses.value.findIndex(c => c.id === courseId)
      if (index !== -1) {
        courses.value[index] = res.data
      }

      return res.data
    } catch (err) {
      console.error('Failed to update course', err)
      throw err
    }
  }

  const deleteCourse = async (courseId) => {
    try {
      await api.delete(`/courses/${courseId}`)
      courses.value = courses.value.filter(c => c.id !== courseId)
    } catch (err) {
      console.error('Failed to delete course', err)
    }
  }

  return {
    coursesList,
    getCoursesByLanguage,
    getCourseById,
    createCourse,
    updateCourse,
    deleteCourse
  }
})