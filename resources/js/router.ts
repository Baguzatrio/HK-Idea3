import { createRouter, createWebHistory } from 'vue-router';

// Auth Pages
import Login from './Pages/Auth/Login.vue';
import Register from './Pages/Auth/Register.vue';
import ForgotPassword from './Pages/Auth/ForgotPassword.vue';
import ResetPassword from './Pages/Auth/ResetPassword.vue';
import VerifyEmail from './Pages/Auth/VerifyEmail.vue';
import ConfirmPassword from './Pages/Auth/ConfirmPassword.vue';

// Main Pages
import Welcome from './Pages/Welcome.vue';
import Dashboard from './Pages/Dashboard.vue';

// Master Data Pages
import UsersIndex from './Pages/MasterData/User/Index.vue';
import RolesIndex from './Pages/MasterData/Role/Index.vue';
import PermissionsIndex from './Pages/MasterData/Permission/Index.vue';
import DivisiIndex from './Pages/MasterData/Divisi/Index.vue';

const routes = [
    {
        path: '/',
        name: 'Welcome',
        component: Welcome,
    },
    {
        path: '/login',
        name: 'Login',
        component: Login,
    },
    {
        path: '/register',
        name: 'Register',
        component: Register,
    },
    {
        path: '/forgot-password',
        name: 'ForgotPassword',
        component: ForgotPassword,
    },
    {
        path: '/reset-password/:token',
        name: 'ResetPassword',
        component: ResetPassword,
    },
    {
        path: '/verify-email',
        name: 'VerifyEmail',
        component: VerifyEmail,
    },
    {
        path: '/confirm-password',
        name: 'ConfirmPassword',
        component: ConfirmPassword,
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: Dashboard,
    },
    // Master Data
    {
        path: '/users',
        name: 'Users',
        component: UsersIndex,
    },
    {
        path: '/roles',
        name: 'Roles',
        component: RolesIndex,
    },
    {
        path: '/permissions',
        name: 'Permissions',
        component: PermissionsIndex,
    },
    {
        path: '/divisis',
        name: 'Divisis',
        component: DivisiIndex,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
