import { createRouter, createWebHistory } from 'vue-router';
import Login from '@/pages/login.vue'; // Импортируйте ваш компонент входа
import Start from '@/pages/start.vue'; // Импортируйте ваш компонент стартовой страницы
import ProfileSettings from '@/pages/profile_settings.vue'; 
import Stamms from '@/pages/stamms.vue'; 

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
    {
        path: '/stamms',
        name: 'Stamms',
        component: Stamms // Указываем компонент для страницы входа
    },
    {
        path: '/profile_settings',
        name: 'ProfileSettings',
        component: ProfileSettings // Указываем компонент для страницы входа
    },
    // Другие маршруты
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
