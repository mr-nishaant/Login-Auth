// src/components/Home.js
import React, { useState, useEffect } from 'react';
import { getUserInfo } from '../utils/api';
import { logout } from '../utils/api';
import './App.css';

const Home = () => {
    const [userInfo, setUserInfo] = useState(null);

    useEffect(() => {
        const fetchUserInfo = async () => {
            try {
                const data = await getUserInfo();
                setUserInfo(data);
            } catch (error) {
                console.error('Error fetching user information:', error);
                // If there is an error (e.g., invalid token), redirect to login
                logout();
            }
        };

        fetchUserInfo();
    }, []);

    return (
        <div className="home">
            <h2>Home Page</h2>
            {userInfo ? (
                <div>
                    <p>Username: {userInfo.username}</p>
                    <p>Email: {userInfo.email}</p>
                    {userInfo.last_login && (
                        <p>Last Login: {new Date(userInfo.last_login).toLocaleString()}</p>
                    )}
                    <button onClick={logout}>Sign Out</button>
                </div>
            ) : (
                <p>Loading...</p>
            )}
        </div>
    );
};

export default Home;
