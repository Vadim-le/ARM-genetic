<template>
  <v-app>
    <v-main class="d-flex align-center justify-center" style="height: 100vh; background-color: #3284e0;">
      <v-container>
        <v-row class="d-flex justify-center">
          <v-col cols="12" md="3"> <!-- Адаптивное количество колонок -->
            <v-card align="center" class="custom-card">
              <v-card-title>{{ isLogin ? 'Вход в аккаунт' : 'Регистрация' }}</v-card-title>
              <v-card-text>
                <v-form v-if="isLogin">
                  <v-text-field
                    v-model="username"
                    label="Почта"
                    prepend-inner-icon="mdi-account"
                  ></v-text-field>
                  <v-text-field
                    v-model="password"
                    label="Пароль"
                    type="password"
                    prepend-inner-icon="mdi-lock"
                  ></v-text-field>
                  <v-btn block @click="login" class="custom-button">SIGN IN</v-btn>
                  <v-btn text block @click="toggleForm">Don't have an account? Sign Up</v-btn>
                </v-form>
                <v-form v-else>
                  <v-text-field
                    v-model="newUsername"
                    label="Username"
                    prepend-inner-icon="mdi-account"
                  ></v-text-field>
                  <v-text-field
                    v-model="newEmail"
                    label="Email"
                    prepend-inner-icon="mdi-email"
                  ></v-text-field>
                  <v-text-field
                    v-model="newPassword"
                    label="Password"
                    type="password"
                    prepend-inner-icon="mdi-lock"
                  ></v-text-field>
                  <v-btn block @click="register" class="custom-button">REGISTER</v-btn>
                  <v-btn text block @click="toggleForm">Already have an account? Sign In</v-btn>
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
      isLogin: true,
      username: '',
      password: '',
      newUsername: '',
      newEmail: '',
      newPassword: ''
    };
  },
  methods: {
    toggleForm() {
      this.isLogin = !this.isLogin;
    },
    async login() {
      // Логика входа
    },
    async register() {
      // Логика регистрации
    }
    
  }
  
};
</script>

<style scoped>
.custom-card {
  background-color: #FDFFF5;
  border-radius: 20px;
  padding: 20px;
}

.custom-button {
  background-color: #3284e0; /* Зеленый фон */
  color: #FDFFF5; /* Белый текст */
  border-radius: 20px; /* Закругленные углы */
  padding: 10px 20px; /* Отступы */
  font-size: 16px; /* Размер шрифта */
  transition: background-color 0.3s; /* Плавный переход */
  border: 2px solid #3284e0;
}

.custom-button:hover {
  background-color: #FDFFF5; /* Цвет при наведении */
  color: #3284e0;
  border-color: #3284e0;
}
</style>
