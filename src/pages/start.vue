<template>
    <div class="area">
      <div class="context">
      </div>
      <div class="container">
        <div class="text-container" v-if="showText">
          <h1 class="fade-in">Добро пожаловать на нашу страницу!</h1>
          <h1 class="fade-in">Добро пожаловать на нашу страницу!</h1>
          <h1 class="fade-in">Добро пожаловать на нашу страницу!</h1>

        </div>
        <div class="button-container" v-if="showButtons">
          <button @click="login" class="btn">Войти</button>
          <button @click="register" class="btn">Зарегистрироваться</button>
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
        }, 1000); // Задержка перед появлением кнопок
      }, 500); // Задержка перед появлением текста
    },
    methods: {
        login() {
            this.$router.push({ name: 'Login' });
        },
      register() {
        this.$router.push({ name: 'Stamms' });
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
    margin-top: 20px;
    opacity: 0;
    animation: fadeIn 1s ease-in-out forwards;
    animation-delay: 1s; /* Задержка перед анимацией кнопок */
  }
  
  .btn {
    margin: 5px;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    border: none;
    border-radius: 5px;
    background-color: #007bff;
    color: white;
    transition: background-color 0.3s;
  }
  
  .btn:hover {
    background-color: #0056b3;
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
  