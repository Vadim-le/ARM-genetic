<template>
  <v-app>
    <v-navigation-drawer v-model="drawer" app>
      <v-list v-if="isAdmin">
    <v-list-item 
      @click="toggleStammDetails" 
      :class="{ 'selected-category': selectedCategory === 'storage_data' }">
      Хранилище данных
    </v-list-item>
    <v-list v-show="showStammDetails" class="pl-4">
      <v-list-item 
        @click="selectPage('storage_stamms')" 
        :class="{ 'selected-category': selectedCategory === 'storage_stamms' }">
        Хранилище штаммов
      </v-list-item>
      <v-list-item 
        @click="selectPage('storage_proteins')" 
        :class="{ 'selected-category': selectedCategory === 'storage_proteins' }">
        Хранилище белка
      </v-list-item>
    </v-list>
    <v-list-item 
      @click="toggleToolsDetails" 
      :class="{ 'selected-category': selectedCategory === 'tools' }">
      Инструменты расчета
    </v-list-item>
    <v-list v-show="showToolsDetails" class="pl-4">
      <v-list-item 
        @click="selectPage('protein_analyze')" 
        :class="{ 'selected-category': selectedCategory === 'protein_analyze' }">
        Полный анализ белка
      </v-list-item>
      <v-list-item 
        @click="selectPage('tool_hydrophobic')" 
        :class="{ 'selected-category': selectedCategory === 'tool_hydrophobic' }">
        Поиск cas-генов
      </v-list-item>
      <v-list-item 
        @click="selectPage('tool_mass')" 
        :class="{ 'selected-category': selectedCategory === 'tool_mass' }">
        Поиск повторяющихся последовательнстей
      </v-list-item>
      <v-list-item 
        @click="selectPage('tool_amino')" 
        :class="{ 'selected-category': selectedCategory === 'tool_amino' }">
        Анализ аминокислотного состава
      </v-list-item>
      <v-list-item 
        @click="selectPage('tool_iso')" 
        :class="{ 'selected-category': selectedCategory === 'tool_iso' }">
        Определение изоэлектрической точки
      </v-list-item>
    </v-list>
      <v-list-item 
      @click="toggleAnalyzeToolsDetails" 
      :class="{ 'selected-category': selectedCategory === 'analyze_tools' }">
      Инструменты анализа
    </v-list-item>
    <v-list v-show="toggleAnalyzeToolsDetails" class="pl-4">
      <v-list-item 
        @click="selectPage('nucleotide_analyze')" 
        :class="{ 'selected-category': selectedCategory === 'nucleotide_analyze' }">
        Анализ нуклеотидов
      </v-list-item>
    </v-list>
    
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
          <v-list-item @click="goToSettings">Настройки</v-list-item>
          <v-list-item @click="logout">Выйти</v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>
      <v-main class="flex-grow-1">
        <v-container fluid class="pa-0">
          <transition name="fade" mode="out-in">
            <component :is="currentPage"></component>
          </transition>
        </v-container>
      </v-main>
    </v-app>
</template>

<script>
import Page1 from './stamms.vue';
import StorageStamms from './stamms.vue'; // Импортируем компонент хранилища штаммов
import StorageProteins from './profile_settings.vue'; // Импортируем компонент хранилища белка
import ProteinAnalyze from './protein_analyze.vue';
import NucleotideAnalyze from './nucleotide_analyze.vue';
import { mapGetters } from 'vuex'; // Импортируем mapGetters для доступа к геттерам Vuex


export default {
  data() {
    return {
      currentPage: 'Page1', // По умолчанию отображается Page1
      drawer: false, // Состояние для управления видимостью меню
      showStammDetails: false, // Состояние для управления видимостью деталей штаммов
      showToolsDetails: false, // Состояние для управления видимостью деталей штаммов
      showAnalzeToolsDetails: false,
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
      this.$router.push({ name: 'Profile' });
    },
    goToSettings() {
      // Логика для перехода к профилю пользователя
      this.$router.push({ name: 'ProfileSettings' });
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
    toggleToolsDetails() {
      this.showToolsDetails = !this.showToolsDetails; 
    },
    toggleAnalyzeToolsDetails() {
      this.showAnalyzeToolsDetails = !this.showAnalyzeToolsDetails; 
    },
    selectPage(page) {
    console.log('Текущая роль пользователя:', this.userRole); // Выводим роль в консоль

    // Объект для сопоставления страниц с компонентами
    const pageMap = {
      storage_stamms: 'StorageStamms',
      storage_proteins: 'StorageProteins',
      protein_analyze: 'ProteinAnalyze',
      nucleotide_analyze: 'NucleotideAnalyze'
    };

    // Устанавливаем текущую страницу на компонент из объекта, или на форматированное имя
    this.currentPage = pageMap[page] || page.charAt(0).toUpperCase() + page.slice(1);
    
    // Устанавливаем выбранную категорию
    this.selectedCategory = page;

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
    ProteinAnalyze,
    NucleotideAnalyze,
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
<style scoped>
.selected-category {
  background-color: green; /* Задаем зеленый фон для выделенной категории */
  color: white; /* Задаем белый цвет текста для контраста */
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 2.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active в <2.1.8 */ {
  opacity: 0;
}
</style>
