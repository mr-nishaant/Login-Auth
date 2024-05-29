// src/components/Login.js
import React, { useState } from 'react';
import { login } from '../utils/api';
import { useNavigate } from 'react-router-dom';
import { Link } from 'react-router-dom';
import './App.css';

const Login = () => {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState(null);
    const navigate = useNavigate();

    const handleSubmit = async (e) => {
        e.preventDefault();
        const data = await login(username, password);
        if (data.token) {
            navigate('/');
        } else {
            setError(data.message);
        }
    };
    return (
        <div>
            <h1>Login</h1>
            {error && <p style={{ color: 'red' }}>{error}</p>}
            <form onSubmit={handleSubmit}>
                <input
                    type="text"
                    placeholder="Username"
                    value={username}
                    onChange={(e) => setUsername(e.target.value)}
                /><br /><br />
                <input
                    type="password"
                    placeholder="Password"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                /><br /><br />
                <button type="submit">Login</button>
                <Link to="/Signup">
                <button>Signup </button>
                </Link>
            </form>
            
        </div>
    );
};

export default Login;
