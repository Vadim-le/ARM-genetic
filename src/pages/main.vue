<template>
  <v-app>
    <v-navigation-drawer v-model="drawer" app>
      <v-list v-if="isAdmin">
        <v-list-item @click="toggleStammDetails">Хранилище данных</v-list-item>
        <v-list v-show="showStammDetails">
          <v-list-item @click="selectPage('storage_stamms')">Хранилище штаммов</v-list-item>
          <v-list-item @click="selectPage('storage_proteins')">Хранилище белка</v-list-item>
        </v-list>
        <v-list-item @click="toggleStammDetails">Инструменты анализа</v-list-item>
      </v-list>

      <v-list v-else>
        <v-list-item @click="selectPage('page1')"> 1</v-list-item>
        <v-list-item @click="selectPage('page2')"> 2</v-list-item>
        <v-list-item @click="selectPage('page3')"> 3</v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-app-bar app>
      <v-btn icon @click="toggleDrawer">
        <v-icon>mdi-menu</v-icon>
      </v-btn>
      <v-toolbar-title>Мое приложение</v-toolbar-title>
      <v-spacer></v-spacer>
      <!-- Иконка пользователя с выпадающим меню -->
      <v-menu offset-y>
        <template v-slot:activator="{ props }">
          <v-btn  icon variant="text"v-bind="props">
            <v-avatar>
              <img src="https://avatars.dzeninfra.ru/get-zen_doc/3644482/pub_612df88f8079432d48f7dbb9_6151bebeeebd2f0145c4680e/scale_1200" alt="User Photo" style="width: 100%; height: 100%;" />           
            </v-avatar>
          </v-btn>
        </template>
        <v-list>
          <v-list-item @click="goToProfile">Профиль</v-list-item>
          <v-list-item @click="logout">Выйти</v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>

    <v-main class="flex-grow-1">
      <v-container fluid class="pa-0">
        <component :is="currentPage"></component>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import Page1 from './stamms.vue';
import StorageStamms from './stamms.vue'; // Импортируем компонент хранилища штаммов
import StorageProteins from './profile_settings.vue'; // Импортируем компонент хранилища белка
import { mapGetters } from 'vuex'; // Импортируем mapGetters для доступа к геттерам Vuex

export default {
  data() {
    return {
      currentPage: 'Page1', // По умолчанию отображается Page1
      drawer: false, // Состояние для управления видимостью меню
      showStammDetails: false, // Состояние для управления видимостью деталей штаммов
    };
  },
  computed: {
    ...mapGetters(['userRole']), // Получаем роль пользователя из Vuex
    isAdmin() {
      return Array.isArray(this.userRole) && this.userRole.includes('admin'); // Проверяем, является ли пользователь администратором
    },
  }, 
  methods: {
    goToProfile() {
      // Логика для перехода к профилю пользователя
      console.log("Переход к профилю");
    },
    async logout() {
      console.log('Выход из аккаунта');
      try {
        const token = localStorage.getItem('token'); // Извлекаем токен
        const response = await fetch(`http://localhost:8000/api/auth/logout`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`, // Добавляем токен в заголовок
            'Content-Type': 'application/json',
          },
        });

        if (!response.ok) {
          throw new Error('Ошибка сети: ' + response.statusText);
        }
      } catch (error) {
        console.error('Ошибка при получении данных:', error);
      }
      
      localStorage.removeItem('token'); // Удаляем токен
      this.$store.commit('resetUserData'); // Сброс данных пользователя

      console.log("Выход из системы");
      this.$router.push({ name: 'Login' }); // Перенаправляем на страницу входа
    },


    // Переключаем видимость деталей штаммов
    toggleStammDetails() {
      this.showStammDetails = !this.showStammDetails; 
    },
    selectPage(page) {
      console.log('Текущая роль пользователя:', this.userRole); // Выводим роль в консоль
      this.currentPage = page.charAt(0).toUpperCase() + page.slice(1); // Преобразуем в формат компонента
      if (page === 'storage_stamms') {
        this.currentPage = 'StorageStamms'; // Устанавливаем текущую страницу на компонент хранилища штаммов
      } else if (page === 'storage_proteins') {
        this.currentPage = 'StorageProteins'; // Устанавливаем текущую страницу на компонент хранилища белка
      }
      this.drawer = false; // Закрываем меню при выборе страницы
    },
    toggleDrawer() {
      this.drawer = !this.drawer; // Переключаем состояние меню
    },
  },
  components: {
    Page1,
    StorageStamms,
    StorageProteins,
  },
  mounted() {
    // Устанавливаем текущую страницу в зависимости от роли пользователя
    if (this.isAdmin) {
      this.currentPage = 'Page1'; // Или любую другую страницу для администраторов
    } else {
      this.currentPage = 'Page1'; // Или любую другую страницу для неадминистраторов
    }
  },
};
</script>
