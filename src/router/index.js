import { createRouter, createWebHistory } from 'vue-router';
import Login from '@/pages/login.vue'; // Импортируйте ваш компонент входа
import Start from '@/pages/start.vue'; // Импортируйте ваш компонент стартовой страницы
import ProfileSettings from '@/pages/profile_settings.vue'; 
import Stamms from '@/pages/stamms.vue'; 
import Main from '@/pages/main.vue';
import Profile from '@/pages/profile.vue';
import SequenceManagement from '@/pages/sequence_management.vue';
import ProteinAnalyze from '@/pages/protein_analyze.vue';
import NucleotideAnalyze from '@/pages/nucleotide_analyze.vue';
import FindRepeats from '@/pages/find_repeats.vue';





const routes = [
    {
        path: '/',
        name: 'Home',
        redirect: '/start'
    },
    {
        path: '/start',
        name: 'Start',
        component: Start
    },
    {
        path: '/login',
        name: 'Login',
        component: Login
    },
    {
        path: '/main',
        name: 'Main',
        component: Main
    },
    {
        path: '/profile_settings',
        name: 'ProfileSettings',
        component: ProfileSettings
    },
    {
        path: '/profile',
        name: 'Profile',
        component: Profile
    },
    {
        path: '/sequence_management/:id',
        name: 'SequenceManagement',
        component: SequenceManagement
    },
    {
        path: '/protein_analyze',
        name: 'ProteinAnalyze',
        component: ProteinAnalyze
    },
    {
        path: '/nucleotide_analyze',
        name: 'NucleotideAnalyze',
        component: NucleotideAnalyze
    },
    {
        path: '/find_repeats',
        name: 'FindRepeats',
        component: FindRepeats
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
