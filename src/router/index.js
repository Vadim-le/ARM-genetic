import { createRouter, createWebHistory } from 'vue-router';
import Login from '@/pages/login.vue'; // Импортируйте ваш компонент входа
import Start from '@/pages/start.vue'; // Импортируйте ваш компонент стартовой страницы

const routes = [
    {
        path: '/',
        name: 'Home',
        redirect: '/start' // Перенаправление на страницу start
    },
    {
        path: '/start',
        name: 'Start',
        component: Start // Указываем компонент для стартовой страницы
    },
    {
        path: '/login',
        name: 'Login',
        component: Login // Указываем компонент для страницы входа
    },
    // Другие маршруты
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
