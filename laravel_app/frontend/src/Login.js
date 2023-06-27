import React from 'react';
import { Form, Input, Button, Checkbox } from 'antd';
import { UserOutlined, LockOutlined } from '@ant-design/icons';
import axios from 'axios';
import { redirect, useNavigate } from 'react-router-dom';
import getCsrfToken from './axiosConfig';

import save_log from './logService';

const Login = ({ onLogin }) => {
  const nav = useNavigate();

  const onFinish = (values) => {
  axios.post('http://localhost:2000/api/login', values)
    .then((response) => {
      // console.log('Login success:', response.data);

      const token = response.data.token;
      
      onLogin(token);

      axios.get('http://localhost:2000/api/get_user_token', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })

      axios.get('http://localhost:2000/api/get_auth_user', {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
        .then((response) => {
          // console.log('Token used to acces data', token);
          save_log('Ienacis jauns lietotajs! Epasts: ' + response.data.email + ', vards: ' + response.data.name + ', loma: ' + response.data.role + '!', token);
          // console.log(':', userDataResponse.data);
          // Redirect to the home page or any other desired route
          nav('/home');
        })
    })
    .catch((error) => {
      // console.error('Login error:', error.response.data);
    });
};


  return (
    <div>

      <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', height: '100vh' }}>
        <Form
          name="loginForm"
          onFinish={onFinish}
          style={{ maxWidth: 300 }}
        >
          <Form.Item
            name="email"
            rules={[
              { required: true, message: 'Lūdzu, ievadiet savu e-pasta adresi!' },
              { type: 'email', message: 'Lūdzu, ievadiet derīgu e-pasta adresi!' },
            ]}
          >
            <Input prefix={<UserOutlined />} placeholder="E-pasta adrese" />
          </Form.Item>
          <Form.Item
            name="password"
            rules={[
              { required: true, message: 'Lūdzu, ievadiet savu paroli!' },
            ]}
          >
            <Input.Password prefix={<LockOutlined />} placeholder="Parole" />
          </Form.Item>
          <Form.Item>
            <Form.Item name="remember" valuePropName="checked" noStyle>
              <Checkbox>Atcereties mani</Checkbox>
            </Form.Item>
            <a href="/forgot-password" style={{ float: 'right' }}>
              Aizmirsāt paroli?
            </a>
          </Form.Item>
          <Form.Item>
            <Button type="primary" htmlType="submit" style={{ width: '100%' }}>
              Ieiet
            </Button>
          </Form.Item>
        </Form>
      </div>
    </div>
  );
};

export default Login;
