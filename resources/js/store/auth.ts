import { reactive } from 'vue';
import axios from 'axios';

export const authState = reactive({
    user: null as any,
    is_super_admin: false,
    permissions: [] as string[],
    loaded: false,
});

export const fetchAuthUser = async (force = false) => {
    if (authState.loaded && !force) return authState.user;
    try {
        const response = await axios.get('/api/user');
        authState.user = response.data;
        // Check if user has super_admin role
        const roles = response.data.roles || [];
        authState.is_super_admin = roles.some((r: any) => r.name === 'super_admin');
        authState.loaded = true;
        return authState.user;
    } catch (error) {
        authState.loaded = true;
        authState.user = null;
        authState.is_super_admin = false;
        return null;
    }
};
