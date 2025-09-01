<template>
  <div id="app">
    <Navbar />

    <main class="container">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import Navbar from "@/components/Navbar.vue";
</script>

<style>
body {
  margin: 0;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  background: #f4f6f9;
}

#app {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

main.container {
  flex: 1;
  padding: 20px;
}
</style>
