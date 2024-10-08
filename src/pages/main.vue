<template>
	<v-app>
		<v-navigation-drawer v-model="drawer" app>
			<v-list v-if="isAdmin">
				<v-list-item @click="toggleStammDetails" :class="{ 'selected-category': selectedCategory === 'storage_data' }">
					Хранилище данных
				</v-list-item>
				<v-list v-show="showStammDetails" class="pl-4">
					<v-list-item @click="selectPage('storage_stamms')" :class="{ 'selected-category': selectedCategory === 'storage_stamms' }">
						Хранилище штаммов
					</v-list-item>
				</v-list>
				<v-list-item @click="toggleToolsDetails" :class="{ 'selected-category': selectedCategory === 'tools' }">
					Инструменты расчета
				</v-list-item>
				<v-list v-show="showToolsDetails" class="pl-4">
					<v-list-item @click="selectPage('protein_analyze')" :class="{ 'selected-category': selectedCategory === 'protein_analyze' }">
						Полный анализ белка
					</v-list-item>
					<v-list-item @click="selectPage('find_repeats')" :class="{ 'selected-category': selectedCategory === 'find_repeats' }">
						Поиск повторяющихся последовательнстей
					</v-list-item>
				</v-list>
				<v-list-item @click="toggleAnalyzeToolsDetails" :class="{ 'selected-category': selectedCategory === 'analyze_tools' }">
					Инструменты анализа
				</v-list-item>
				<v-list v-show="showAnalyzeToolsDetails" class="pl-4">
					<v-list-item @click="selectPage('nucleotide_analyze')" :class="{ 'selected-category': selectedCategory === 'nucleotide_analyze' }">
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
			<v-toolbar-title>АРМ Генетик</v-toolbar-title>
			<v-spacer></v-spacer>
			<!-- Иконка пользователя с выпадающим меню -->
			<v-menu offset-y>
				<template v-slot:activator="{ props }">
					<v-btn icon variant="text" v-bind="props">
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
import FindRepeats from './find_repeats.vue';
import StorageStamms from './stamms.vue';
import StorageProteins from './profile_settings.vue'; 
import ProteinAnalyze from './protein_analyze.vue';
import NucleotideAnalyze from './nucleotide_analyze.vue';
import { mapGetters } from 'vuex'; 


export default {
  data() {
    return {
      currentPage: 'Page1',
      drawer: false,
      showStammDetails: false, 
      showToolsDetails: false, 
      showAnalyzeToolsDetails: false,
    };
  },
  computed: {
    ...mapGetters(['userRole']),
    isAdmin() {
      return Array.isArray(this.userRole) && this.userRole.includes('admin'); 
    },
  }, 
  methods: {
    goToProfile() {
      this.$router.push({ name: 'Profile' });
    },
    goToSettings() {
      this.$router.push({ name: 'ProfileSettings' });
    },
    async logout() {
      console.log('Выход из аккаунта');
      try {
        const token = localStorage.getItem('token'); 
        const response = await fetch(`http://localhost:8000/api/auth/logout`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
          },
        });

        if (!response.ok) {
          throw new Error('Ошибка сети: ' + response.statusText);
        }
      } catch (error) {
        console.error('Ошибка при получении данных:', error);
      }
      
      localStorage.removeItem('token'); 
      this.$store.commit('resetUserData');

      console.log("Выход из системы");
      this.$router.push({ name: 'Login' });
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
    selectPage(page, strainName) {
  console.log('Текущая роль пользователя:', this.userRole); 

  const pageMap = {
    storage_stamms: 'StorageStamms',
    storage_proteins: 'StorageProteins',
    protein_analyze: 'ProteinAnalyze',
    nucleotide_analyze: 'NucleotideAnalyze',
    find_repeats: 'FindRepeats',
  };

  this.currentPage = pageMap[page] || page.charAt(0).toUpperCase() + page.slice(1);
  
  this.selectedCategory = page;

  if (strainName) {
    this.$refs.NucleotideAnalyze.strainName = strainName;
  }

  this.drawer = false; 
},
    toggleDrawer() {
      this.drawer = !this.drawer;
    },
  },
  components: {
    Page1,
    StorageStamms,
    StorageProteins,
    ProteinAnalyze,
    NucleotideAnalyze,
    FindRepeats,
  },
  mounted() {
    if (this.isAdmin) {
      this.currentPage = 'Page1'; 
    } else {
      this.currentPage = 'Page1';
    }
  },
};
</script>
<style scoped>
.selected-category {
  background-color: green; 
  color: white;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}
</style>
