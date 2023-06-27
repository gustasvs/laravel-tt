import React from 'react';
import { Menu, Row } from 'antd';

const Navbar = () => {
  return (
    <Row justify="space-between" align="middle">
      <Menu mode="horizontal">
        <Menu.Item key="home">
          <a href="/home">Home</a>
        </Menu.Item>
        <Menu.Item key="images">
          <a href="/images">Images</a>
        </Menu.Item>
        <Menu.Item key="users">
          <a href="/users">Users</a>
        </Menu.Item>
      </Menu>

      <Menu mode="horizontal">
        <Menu.Item key="login">
          <a href="{{ route('login') }}">Login</a>
        </Menu.Item>
        <Menu.Item key="register">
          <a href="{{ route('register') }}">Register</a>
        </Menu.Item>
      </Menu>
    </Row>
  );
};

export default Navbar;
