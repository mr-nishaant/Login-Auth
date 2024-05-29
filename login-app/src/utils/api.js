// src/utils/api.js
import Cookies from 'js-cookie';

const API_URL = 'http://localhost/login-auth/server/wp-json/custom-auth/v1';

export const signup = async (username, email, password) => {
    const res = await fetch(`${API_URL}/signup`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ username, email, password })
    });
    return res.json();
};

export const login = async (username, password) => {
    const res = await fetch(`${API_URL}/login`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ username, password })
    });
    const data = await res.json();
    if (data.token) {
        Cookies.set('token', data.token);
    }
    return data;
};

export const getUserInfo = async () => {
    const token = Cookies.get('token');
    const res = await fetch(`${API_URL}/user`, {
        method: 'GET',
        headers: {
            'Authorization': token
        }
    });
    return res.json();
};
export const logout = () => {
    Cookies.remove('token'); // Remove the token from cookies
    window.location.href = '/login'; // Redirect to the login page
};
