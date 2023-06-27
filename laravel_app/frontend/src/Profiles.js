import React, { useEffect, useState } from 'react';
import { Table, Switch, Avatar, Button } from 'antd';
import { BrowserRouter as Router, Route, Routes, Link } from 'react-router-dom';
import axios from 'axios';

import './App.css';
import './Profiles.css';

import save_log from './logService';

const UserProfilePage = ( {token} ) => {
  const [auth_user, setUser] = useState(null);
  useEffect(() => {
    if (token) {
      axios.get(`http://localhost:2000/api/get_auth_user`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })

        .then((response) => {
          console.log('Authenticated User Data:', response.data);
        
          setUser(response.data);
        })
        .catch((error) => {
          console.error('Error fetching user data:', error);
        });
    }
  }, [token]);

  const [users, setUsers] = useState([]);

  useEffect(() => {
    axios.get('http://localhost:2000/api/users')
      .then(response => {
        setUsers(response.data);
      })
      .catch(error => {
        console.error(error);
      });
  }, []);

  const columns = [
    {
      title: 'Profila bilde',
      dataIndex: 'profile_picture_path',
      key: 'profile_picture_path',
      render: (profile_picture_path) => <Avatar src={'http://localhost:2000/storage/images/' + profile_picture_path} />,
    },
    {
      title: 'Vards',
      dataIndex: 'name',
      key: 'name',
      render: (name, user) => <Link to={`/profile/${user.id}`}>{name}</Link>,
    },
    {
      title: 'Epasts',
      dataIndex: 'email',
      key: 'email',
    },
    {
      title: 'Admina privilegijas',
      dataIndex: 'role',
      key: 'role',
      // if (auth_user.role !== 'admin')
      render: (role, user) => (
        <Switch checked={role === 'admin'} onChange={(checked) => handleRoleChange(checked, user)} 
          disabled={!auth_user || (auth_user && auth_user.role !== 'admin')}
          className={!auth_user || (auth_user && auth_user.role !== 'admin') ? 'grayscale-button' : ''}
        />
      ),
    },
  ];

  const handleRoleChange = (checked, user) => {
    const updatedUsers = users.map((u) =>
      u.id === user.id ? { ...u, role: checked ? 'admin' : 'user' } : u
    );
    setUsers(updatedUsers);
    axios.put(`http://localhost:2000/api/users/${user.id}`, {}, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      save_log("Nomainīta loma lietotājam " + user.name, token);
      // console.log('desc uploaded successfully:', response.data);
    })
    .catch((error) => {
      console.log({error});
      save_log('Neizdevas nomainīt lomu lietotājam.' + user.name, " ar kļūdu: " + error, token);
    });
  };

  return (
    <>
      <div className='page-header'>
        Profilu saraksts
      </div>

      <div style={{ margin: '16px' }} className='table-container'>
        <Table dataSource={users} columns={columns} rowKey="id" />
      </div>
    </>
  );
};

export default UserProfilePage;
