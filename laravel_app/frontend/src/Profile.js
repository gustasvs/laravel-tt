import React, { useState, useEffect } from 'react';
import { Card, Col, Row, Avatar, Button, Input, Upload, message, Spin} from 'antd';
import { useParams, useNavigate} from 'react-router-dom';
import axios from 'axios';
// import { debounce } from 'lodash'; 

import './App.css';

import save_log from './logService';

const Profile = ( { token } ) => {
  const nav = useNavigate();
  const [auth_user, setAuthUser] = useState(null);
  const { id } = useParams();
  const [user, setUser] = useState(null);
  const [userImages, setUserImages] = useState([]);

  useEffect(() => {
    axios.get(`http://localhost:2000/api/user/${id}`)
      .then((response) => {
        setUser(response.data);
      })
      .catch((error) => {
        console.error('Error fetching user data:', error);
      });
  }, [id]);

  useEffect(() => {
    if (token) {
      axios.get(`http://localhost:2000/api/get_some_user_images/${id}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }).then((response) => {
          const imagesWithUrls = response.data.map((image) => ({
            ...image,
            url: `http://localhost:2000/storage/images/${image.filename}`, // Replace with the base URL of your file server
          }));
          setUserImages(imagesWithUrls);
          console.log(response);
        }).catch((error) => {
          console.error('Error fetching user image data:', error);
        });
    } else {
      setUserImages([]);
    }
    if (token) {
      axios.get(`http://localhost:2000/api/get_auth_user`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
        .then((response) => {
          console.log('Authenticated User Data:', response.data);
        
          setAuthUser(response.data);
        })
        .catch((error) => {
          console.error('Error fetching user data:', error);
        });
    }
  }, [token, id]);

  const debounce = (func, delay) => {
    let timerId;

    return (...args) => {
      clearTimeout(timerId);

      timerId = setTimeout(() => {
        func(...args);
      }, delay);
    };
  };

  const debounceDelay = 1000;

  const handleNameChange = (event) => {
    const newName = event.target.value;
    const formData = new FormData();
    formData.append('name', newName);
    setUser((prevUser) => ({ ...prevUser, name: newName }));

    axios
      .post(`http://localhost:2000/api/users/${id}/change_name`, formData, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then((response) => {
        save_log('Lietotāja ' + newName + ' vārds pamainīts', token);
      });
  };

  const debouncedHandleNameChange = debounce(handleNameChange, debounceDelay);

  const handleChange = (event) => {
    setUser((prevUser) => ({ ...prevUser, name: event.target.value }));
    debouncedHandleNameChange(event);
  };
  

  const handleProfilePictureChange = (file) => {
    const formData = new FormData();
    formData.append('image', file);
    
    axios
    .post(`http://localhost:2000/api/users/${id}/change_profile_image`, formData, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then(() => {
      message.success('Nomainita profila bilde!');
      
      axios
        .get(`http://localhost:2000/api/user/${id}`)
        .then((response) => {
          setUser(response.data);
          save_log('Lietotajam ' + response.data.name + ' nomainita profila bilde!', token);
        })
    })
    .catch((error) => {
      message.error('Neizdevas nomainit profila bildi');
    });

  };
  const handleUpload = (file) => {
    handleProfilePictureChange(file);
    return false; // Prevent the default upload behavior
  };

  const handleDeleteUser = () => {
    var vards = user.name;
    if (token) {
      axios.post(`http://localhost:2000/api/users/${id}/delete`, {}, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then(response => {
        message.success('Lietotājs izdzēsts!');
        save_log('Lietotājs ' + vards + ' izdzēsts!', token);
        nav('/home');
      })
      .catch(error => {
        console.error('Nevareja izdzest:', error);
      });
    } else {
        console.error('Nav autentifikacijas lai izdzestu');
    }
  };

  if (!user) {
    return <Spin size="large" className='loading'/>;
    return <div>Meklēju lietotāju</div>;
  }

  return (
    <div>
      <Card style={{ marginBottom: '16px' }}>
        <Card.Meta avatar={<Avatar src={'http://localhost:2000/storage/images/' + user.profile_picture_path} />} title={user.name} />
      </Card>
      <h2>Lietotaja bildes</h2>
      {token ? (
      <Row gutter={[8, 8]}>
        {userImages.map((image) => (
          <Col key={image.id} xs={24} sm={12} md={8} lg={6}>
            <Card
              hoverable
              cover={<img alt={image.description} src={image.url} style={{ borderRadius: '8px' }} />}
            >
              <Card.Meta title={image.description} />
            </Card>
          </Col>
        ))}
      </Row>
       ) : (
        <div>Ielogojies, lai varetu redzet bildes!</div>
      )}
      <h2>Mainit profila iestatijumus</h2>
      {token && auth_user  && ((auth_user.name == user.name) || auth_user.role === 'admin') ? ( <>
      <Input
        value={user.name}
        onChange={handleChange}
        style={{ marginBottom: '16px' }}
        placeholder="Jaunais vards"
      />
      <Upload beforeUpload={handleUpload} showUploadList={false}>
        <Button type="primary" style={{ marginBottom: '16px' }}>
          Change Profile Picture
        </Button>
      </Upload>
      <Button type="danger" onClick={handleDeleteUser}>
        Delete User
      </Button> </>
      ) : ( <>
        {auth_user && (auth_user.name === user.name) ? (
        <div>Ielogojies, lai varetu mainit profila datus!</div>
        ) : (<div>Nevar mainit citu lietotaju datus!</div>) 
        } </>
        )}
    </div>
  );
};

export default Profile;
