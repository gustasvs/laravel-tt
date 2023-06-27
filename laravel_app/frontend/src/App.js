import React, { useEffect, useState } from 'react';
import { Table, Switch, Avatar, Button } from 'antd';
import { BrowserRouter as Router, Route, Routes, Link } from 'react-router-dom';
import axios from 'axios';
// import axios from './axiosConfig';
import getCsrfToken from './axiosConfig';
import './App.css';

import Home from './Home'; 
import Profiles from './Profiles'; 
import Register from './Register';
import Login from './Login';
import Profile from './Profile';
import ForgotPassword from './ForgotPassword';
import Logs from './Logs';
import save_log from './logService';


const App = () => {
  const [token, setToken] = useState(localStorage.getItem("token"));
  useEffect(() => {
    if (localStorage.getItem("token"))
      setToken(localStorage.getItem("token"));
  }, [])

  const handleLogin = (token) => {
    setToken(token);
    localStorage.setItem("token", token);
  };
  const handleRegister = (token) => {
    setToken(token);
    save_log("Registrejies jauns lietotajs!", token);
    localStorage.setItem("token", token);
  };

  const handleLogout = () => {
    save_log("Lietotajs izgajis no sistemas!", token);
    setToken('');
    localStorage.removeItem("token");
  };

  return (
    <Router>
      <Navbar token={token} onLogout={handleLogout} />
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/home" element={<Home token={token} />} />
        <Route path="/profiles" element={<Profiles token={token}/>} />
        <Route path="/register" element={<Register onRegister={handleRegister}/>} />
        <Route path="/login" element={<Login onLogin={handleLogin} />} />
        <Route path="/profile/:id" element={<Profile token={token}/>} />
        <Route path="/forgot-password" element={<ForgotPassword />} />
        <Route path="/logs" element={<Logs token={token}/>} />
      </Routes>
    </Router>
  );
};

const Navbar = ({ token, onLogout }) => {

  const [userName, setUserName] = useState('');
  const [userRole, setUserRole] = useState('');
  const [loading, setLoading] = useState(true);
  const [pic_path, setpic_path] = useState('none');

  useEffect(() => {
    setLoading(true);
    if (token) {
      axios
        .get('http://localhost:2000/api/get_auth_user', {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
        .then((userDataResponse) => {
          console.log(userDataResponse.data);
          setUserName(userDataResponse.data.name);
          setpic_path(userDataResponse.data.profile_picture_path);
          setUserRole(userDataResponse.data.role);

          setLoading(false)
        })
        .catch((error) => {
          console.error('Authenticated User Data error:', error.response.data);
        });
    }
    else  setLoading(false);
  }, [token]);

  console.log({token})

  const handleLogout = () => {
    onLogout();
  };
  if (loading) return <div></div>
  return (
    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '16px', backgroundColor: '#f0f0f0', position: 'sticky', top: 0, zIndex: 1 }}>
      <div className='nav-bar'>
        <Link to="/home">
          <Button type="link" className="custom-button">
          <span className="button-text">Mājas</span>
          </Button>
        </Link>
        <Link to="/profiles">
          <Button type="link" className="custom-button">
            Profili
          </Button>
        </Link>
        {(token  && userName && userRole === 'admin' &&
        <Link to="/logs">
          <Button type="link" className="custom-button">
            Sistēmas žurnāla ieraksti
          </Button>
        </Link>
        )}
        {(token) ? (
          <>
          {pic_path !== 'none'  ? (
            <Avatar className="pfp" src={'http://localhost:2000/storage/images/' + pic_path} />
          ) : (
            <></>
          )}
          <span className="athorised-welcome"> Sveicinats {userName}!</span>
          </>
          ) : (
          <span className="guest-welcome">Jūs aplūkojat mājaslapu kā viesis!</span>
        )}
      </div>
      <div className='nav-bar'>
        {token ? (
          <Button type="primary" onClick={handleLogout}>
            Iziet
          </Button>
        ) : ( <>
          <Link to="/register">
            <Button type="link" className="custom-button">
              Reģistrēties
            </Button>
          </Link>
          <Link to="/login">
            <Button type="link" className="custom-button">
              Pieteikties
            </Button>
          </Link>
          </>
          
        )}
      </div>
    </div>
  );
};

export default App;