<template>
  <v-app>
    <v-main class="d-flex align-center justify-center" style="height: 100vh; background-color: #2c2c2c;">
      <v-card width="400" class="pa-4">
        <v-card-title class="justify-center">
          <v-img
            :width="70"
            aspect-ratio="1/1"
            cover
            src="C:/ARM-genetic/frontend/src/images/logo.png"
          ></v-img>
        </v-card-title>
        <v-card-text>
          <v-form v-if="isLogin">
            <v-text-field
              v-model="username"
              label="Username"
              prepend-inner-icon="mdi-account"
            ></v-text-field>
            <v-text-field
              v-model="password"
              label="Password"
              type="password"
              prepend-inner-icon="mdi-lock"
            ></v-text-field>
            <v-btn color="primary" block @click="login">SIGN IN</v-btn>
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
            <v-btn color="primary" block @click="register">REGISTER</v-btn>
            <v-btn text block @click="toggleForm">Already have an account? Sign In</v-btn>
          </v-form>
        </v-card-text>
      </v-card>
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
  }
};
</script>

<style>
.v-application {
  background-color: #2c2c2c;
}
</style>
