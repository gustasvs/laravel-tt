import React from 'react';
import { Form, Input, Button } from 'antd';
import { UserOutlined, LockOutlined } from '@ant-design/icons';
import axios from 'axios';
import { redirect, useNavigate } from 'react-router-dom';

import save_log from './logService';

const Register = ( {onRegister} ) => {
  const nav = useNavigate();
  const onFinish = (values) => {
    console.log('Saņemtas vertibas: ', values);
    axios.post('http://localhost:2000/api/register', values)
    .then((response) => {
      const token = response.data.token;

      
      onRegister(token);

      axios.get('http://localhost:2000/api/get_user_token', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
      .then((tokenResponse) => {
        const userToken = tokenResponse.data.token;
        save_log('Reģistrējies jauns lietotājs!', userToken);
        nav('/home');
      })
      .catch((error) => {
        console.error('User Token error:', error.response.data);
      });
    })
    .catch((error) => {
      console.error('Login error:', error.response.data);
      // Handle login errors here (e.g., display error message, clear form fields, etc.)
    });
  };

  return (
    <div>
      <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', height: '100vh' }}>
        <Form
          name="registerForm"
          onFinish={onFinish}
          style={{ maxWidth: 300 }}
        >
          <Form.Item
            name="name"
            rules={[
              { required: true, message: 'Lūdzu, ievadiet savu vārdu!' },
            ]}
          >
            <Input prefix={<UserOutlined />} placeholder="Vārds" />
          </Form.Item>
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
          <Form.Item
            name="confirmPassword"
            dependencies={['password']}
            rules={[
              { required: true, message: 'Lūdzu, apstipriniet savu paroli!' },
              ({ getFieldValue }) => ({
                validator(_, value) {
                  if (!value || getFieldValue('password') === value) {
                    return Promise.resolve();
                  }
                  return Promise.reject('Paroles nesakrīt!');
                },
              }),
            ]}
          >
            <Input.Password prefix={<LockOutlined />} placeholder="Apstipriniet paroli" />
          </Form.Item>
          <Form.Item>
            <Button type="primary" htmlType="submit" style={{ width: '100%' }}>
              Reģistrēties
            </Button>
          </Form.Item>
        </Form>
      </div>
    </div>
  );
};

export default Register;
