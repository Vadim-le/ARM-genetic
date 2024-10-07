<template>
    <div class="area">
      <div class="context">
      </div>
      <div class="container">
        <div class="text-container" v-if="showText">
          <h1 class="fade-in">Добро пожаловать в мир генетических исследований!</h1>

        </div>
        <div class="button-container" v-if="showButtons">
          <button @click="login" class="btn">Войти / Зарегистрироваться</button>
        </div>
      </div>
      <ul class="circles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
      </ul>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        showText: false,
        showButtons: false,
      };
    },
    mounted() {
      setTimeout(() => {
        this.showText = true;
        setTimeout(() => {
          this.showButtons = true;
        }, 540); // Задержка перед появлением кнопок
      }, 500); // Задержка перед появлением текста
    },
    methods: {
        login() {
            this.$router.push({ name: 'Login' });
        },
      register() {
        this.$router.push({ name: 'Test' });
      },
    },
  };
  </script>
  
  <style scoped>
  @import url('https://fonts.googleapis.com/css?family=Exo:400,700');
  
  * {
    margin: 0;
    padding: 0;
  }
  
  body {
    font-family: 'Exo', sans-serif;
  }
  
  .area {
    background: #4e54c8;
    background: -webkit-linear-gradient(to left, #8f94fb, #4e54c8);
    width: 100%;
    height: 100vh;
    position: relative;
    overflow: hidden;
  }
  
  .context {
    width: 100%;
    position: absolute;
    top: 50vh;
    text-align: center;
  }
  
  .context h1 {
    color: #fff;
    font-size: 50px;
  }
  
  .container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
    position: relative;
    z-index: 1; /* Чтобы текст и кнопки были выше фона */
  }
  
  .text-container {
    position: relative;
    top: 20px;
  }
  
  .fade-in {
    color: #ffffff; /* Замените на нужный вам цвет */
    animation: fadeIn 1s ease-in-out;
  }
  
  .button-container {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin-top: 20px;
}

.btn {
  padding: 12px 24px;
  font-size: 16px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
  cursor: pointer;
  border: none;
  border-radius: 50px;
  background: linear-gradient(135deg, #007bff, #00c6ff);
  color: white;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(0, 123, 255, 0.25);
  position: relative;
  overflow: hidden;
}

.btn:before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0));
  transition: transform 0.3s ease;
  transform: translateX(-100%);
}

.btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 8px rgba(0, 123, 255, 0.3);
}

.btn:hover:before {
  transform: translateX(100%);
}

.btn:active {
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 123, 255, 0.2);
}

.btn-login {
  background: linear-gradient(135deg, #007bff, #00c6ff);
}

.btn-register {
  background: linear-gradient(135deg, #ff7e5f, #feb47b);
}
  
  .circles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
  }
  
  .circles li {
    position: absolute;
    display: block;
    list-style: none;
    width: 20px;
    height: 20px;
    background: rgba(255, 255, 255, 0.2);
    animation: animate 25s linear infinite;
    bottom: -150px;
  }
  
  .circles li:nth-child(1) {
    left: 25%;
    width: 80px;
    height: 80px;
    animation-delay: 0s;
  }
  
  .circles li:nth-child(2) {
    left: 10%;
    width: 20px;
    height: 20px;
    animation-delay: 2s;
    animation-duration: 12s;
  }
  
  .circles li:nth-child(3) {
    left: 70%;
    width: 20px;
    height: 20px;
    animation-delay: 4s;
  }
  
  .circles li:nth-child(4) {
    left: 40%;
    width: 60px;
    height: 60px;
    animation-delay: 0s;
    animation-duration: 18s;
  }
  
  .circles li:nth-child(5) {
    left: 65%;
    width: 20px;
    height: 20px;
    animation-delay: 0s;
  }
  
  .circles li:nth-child(6) {
    left: 75%;
    width: 110px;
    height: 110px;
    animation-delay: 3s;
  }
  
  .circles li:nth-child(7) {
    left: 35%;
    width: 150px;
    height: 150px;
    animation-delay: 7s;
  }
  
  .circles li:nth-child(8) {
    left: 50%;
    width: 25px;
    height: 25px;
    animation-delay: 15s;
    animation-duration: 45s;
  }
  
  .circles li:nth-child(9) {
    left: 20%;
    width: 15px;
    height: 15px;
    animation-delay: 2s;
    animation-duration: 35s;
  }
  
  .circles li:nth-child(10) {
    left: 85%;
    width: 150px;
    height: 150px;
    animation-delay: 0s;
    animation-duration: 11s;
  }
  
  @keyframes animate {
    0% {
      transform: translateY(0) rotate(0deg);
      opacity: 1;
      border-radius: 0;
    }
    100% {
      transform: translateY(-1000px) rotate(720deg);
      opacity: 0;
      border-radius: 50%;
    }
  }
  
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(-20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  </style>
  