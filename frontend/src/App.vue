<template>
  <Navbar />
  <main class="min-h-screen bg-gray-950 text-white">
    <RouterView />
  </main>
  <Footer />
</template>

<script setup>
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'
</script>
