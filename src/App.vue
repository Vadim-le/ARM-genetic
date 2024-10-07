<template>
  <v-app>
    <v-main>
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </v-main>
  </v-app>
</template>


<script setup>
// Импортировать ничего не нужно, так как маршрутизация будет обрабатывать отображение компонентов
</script>

<style>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active в <2.1.8 */ {
  opacity: 0;
}
</style>
