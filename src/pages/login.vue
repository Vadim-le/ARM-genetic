<template>
  <v-app>
    <v-main class="d-flex align-center justify-center" style="height: 100vh; background-color:rgba(50, 132, 224, 0.8);">
      <v-container>
        <v-row class="d-flex justify-center">
          <v-col cols="12" md="3">
            <v-card align="center" class="custom-card">
              <v-card-title>{{ isLogin ? 'Вход в аккаунт' : 'Регистрация' }}</v-card-title>
              <v-card-text>
                <v-form v-if="isLogin">
                  <v-text-field
                    v-model="email"
                    variant="outlined"
                    placeholder="Введите почту"
                    clearable
                    label="Почта"
                    prepend-inner-icon="mdi-account"
                  ></v-text-field>
                  <v-text-field
                    :append-inner-icon="visible ?  'mdi-eye' : 'mdi-eye-off'"
                    :type="visible ? 'text' : 'password'"
                    placeholder="Введите пароль"
                    prepend-inner-icon="mdi-lock-outline"
                    variant="outlined"
                    label="Пароль"
                    @click:append-inner="visible = !visible"
                  ></v-text-field>
                  <v-btn block @click="login" class="custom-button">SIGN IN</v-btn>
                </v-form>
                <v-form v-else>
                  <v-text-field
                    v-model="newSurname"
                    variant="outlined"
                    placeholder="Введите фамилию"
                    clearable
                    label="Фамилия"
                    prepend-inner-icon="mdi-account"
                  ></v-text-field>
                  <v-text-field
                    v-model="newName"
                    variant="outlined"
                    placeholder="Введите имя"
                    clearable
                    label="Имя"
                    prepend-inner-icon="mdi-account"
                  ></v-text-field>
                  <v-text-field
                    v-model="newEmail"
                    variant="outlined"
                    placeholder="Введите почту"
                    clearable
                    label="Почта"
                    prepend-inner-icon="mdi-email"
                  ></v-text-field>
                  <v-text-field
                    v-model="newPassword"
                    :append-inner-icon="visible ?  'mdi-eye' : 'mdi-eye-off'"
                    :type="visible ? 'text' : 'password'"
                    placeholder="Введите пароль"
                    variant="outlined"
                    label="Пароль"
                    @click:append-inner="visible = !visible"
                    prepend-inner-icon="mdi-lock"
                  ></v-text-field>
                  <v-btn block @click="register" class="custom-button">REGISTER</v-btn>
                </v-form>
              </v-card-text>
              <v-card-text class="text-center">
                <a
                  class="text-blue text-decoration-none"
                  href="#"
                  @click.prevent="toggleForm"
                >
                  <span v-if="isLogin">Нет аккаунта? Зарегистрируйся</span>
                  <span v-else>Уже есть аккаунт? Войти</span>
                  <v-icon icon="mdi-chevron-right"></v-icon>
                </a>
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
      repeatvisible: false,
      visible: false,
      isLogin: true,
      username: '',
      password: '',
      newName: '',
      newSurname: '',
      newEmail: '',
      email: '',
      newPassword: '',
      show1: false, // Переменная для показа/скрытия пароля
      rules: {
        required: value => !!value || 'Required.',
        min: v => v.length >= 8 || 'Min 8 characters',
      },
    };
  },
  methods: {
    toggleForm() {
      this.isLogin = !this.isLogin;
    },
    togglePasswordVisibility() {
      this.show1 = !this.show1; // Переключаем видимость пароля
    },
    async login() {
      try {
        const response = await fetch('http://localhost:8000/api/auth/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            username: this.username,
            password: this.password,
          }),
        });

        if (!response.ok) {
          throw new Error('Login failed');
        }

        const data = await response.json();
        console.log('Login successful:', data);
        // Здесь вы можете сохранить токен или выполнить другие действия
      } catch (error) {
        console.error('Error during login:', error);
      }
    },
    async register() {
      try {
        const response = await fetch('http://localhost:8000/api/auth/register', { // Обновленный путь
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            username: this.newUsername,
            email: this.newEmail,
            password: this.newPassword,
          }),
        });

        if (!response.ok) {
          throw new Error('Registration failed');
        }

        const data = await response.json();
        console.log('Registration successful:', data);
        // Здесь вы можете выполнить дополнительные действия, например, перейти на страницу входа
      } catch (error) {
        console.error('Error during registration:', error);
      }
    }
  },
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
