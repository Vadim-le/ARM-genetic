<template>
    <v-app>
      <v-main class="d-flex align-top justify-center" style="height: 100vh; background-color:#FDFFF5;">
        <v-container>
          <v-row class="d-flex justify-center">
            <v-col cols="12" md="2" class="d-flex">
              <v-card class="custom-card d-flex flex-column" style="flex: 1;">
                <v-card-title>Аккаунт</v-card-title>
                <v-card-text>
                  <v-tabs v-model="tab" color="primary" direction="vertical">
                    <v-tab prepend-icon="mdi-account-cog" text="Аккаунт" value="option-1"></v-tab>
                    <v-tab prepend-icon="mdi-shield-lock-outline" text="Безопасность" value="option-2"></v-tab>
                  </v-tabs>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="12" md="5" class="d-flex">
              <v-card class="custom-card d-flex flex-column" style="flex: 1;">
                <v-card-text>
                  <v-form v-if="isLogin">
                    <template v-if="tab === 'option-1'">
                      <v-card-title class="mt-n4 ml-n4">Настройки</v-card-title>
                      <div class="text-subtitle-1 text-medium-emphasis">Имя</div>
                      <v-text-field
                        v-model="name"
                        variant="outlined"
                        placeholder="Введите имя"
                        density="compact"
                        dense
                      ></v-text-field>
                      <div class="text-subtitle-1 text-medium-emphasis">Фамилия</div>
                      <v-text-field
                        v-model="surname"
                        variant="outlined"
                        placeholder="Введите фамилию"
                        density="compact"
                      ></v-text-field>
                      <div class="text-subtitle-1 text-medium-emphasis">Отчество</div>
                      <v-text-field
                        v-model="patronymic"
                        variant="outlined"
                        placeholder="Введите отчество"
                        density="compact"
                      ></v-text-field>
                      <div class="text-subtitle-1 text-medium-emphasis">Дата рождения</div>
                      <div class="text-subtitle-1 text-medium-emphasis">Пол</div>
                    </template>
                    <template v-if="tab === 'option-2'">
                      <v-card-title class="mt-n4 ml-n4">Пароль</v-card-title>
                      <v-text-field
                        v-model="password"
                        variant="outlined"
                        placeholder="Введите пароль"
                        clearable
                        label="Пароль"
                        prepend-inner-icon="mdi-lock"
                      ></v-text-field>
                      <v-text-field
                        v-model="password"
                        variant="outlined"
                        placeholder="Введите пароль"
                        clearable
                        label="Пароль"
                        prepend-inner-icon="mdi-lock"
                      ></v-text-field>
                    </template>
                  </v-form>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-container>
      </v-main>
    </v-app>
  </template>
  
  <script>
  export default {
    data() {
      return {
        isLogin: true, // или false, в зависимости от вашей логики
        name: '',
        surname: '',
        patronymic: '',
        email: '',
        password: '',
        accessPoint: '',
        tab: 'option-1' // Значение по умолчанию для выбранной вкладки
      };
    }
  };
  </script>

<style scoped>
.custom-card {
  background-color: #FDFFF5;
  border-radius: 20px;
  padding: 20px;
  border: 1px solid #3284e0;
}

.text-subtitle-1 {
  margin-top: -15px; /* Задает отступы со всех сторон */
}


</style>