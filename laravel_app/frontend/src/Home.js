import React, { useEffect, useState } from 'react';
import { Avatar, Card, Row, Col, Button, Space, Upload, message, Input, Modal, Spin } from 'antd';
import { LikeOutlined, DislikeOutlined, DeleteOutlined, UploadOutlined, EyeOutlined, SwitcherTwoTone, CalendarOutlined } from '@ant-design/icons';
import axios from 'axios';
import './App.css';

import ImagePopup from './ImagePopup';
import reklama from './images/reklama_1.png';
import bariba from './images/bariba_1.jpg';
import latvija from './images/karogs.jpg';

import save_log from './logService';

const Home = ( { token } ) => {
  const [auth_user, setUser] = useState(null);
  const [galleryImages, setGalleryImages] = useState([]);
  const [users, setUsers] = useState([]);
  const [userImages, setUserImages] = useState([]);
  const [loading, setLoading] = useState(true);
  const [sortBy, setSortBy] = useState('date');

  const fetchUserImages = () => {
    axios
      .get('http://localhost:2000/api/get_user_images', {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then((response) => {
        const imagesWithUrls = response.data.map((image) => ({
          ...image,
          url: `http://localhost:2000/storage/images/${image.filename}`, // Replace with the base URL of your file server
          author_profile_picture_path: `${image.user.profile_picture_path}`,
          author_name: `${image.user.name}`,
        }));
        setUserImages(imagesWithUrls);
      })
      .catch((error) => {
        console.error('Error fetching user image data:', error);
      });
  };
  
  const handleSortBy = () => {
    const newSortBy = sortBy === 'views' ? 'date' : 'views';
    setSortBy(newSortBy);

    const sortedImages = [...galleryImages].sort((a, b) => {
      if (newSortBy === 'views') {
        return a.views - b.views;
      } else if (newSortBy === 'date') {
        return new Date(a.date) - new Date(b.date);
      }
      return 0;
    });
    
    setGalleryImages(sortedImages);
  };

  useEffect(() => {
    setLoading(true);
    if (token) {
      axios.get(`http://localhost:2000/api/get_auth_user`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
        .then((response) => {          
          // save_log('Ieguti autorizeta lietotaja dati', token);
          setUser(response.data);
        })
        .catch((error) => {
          // save_log('Kluda iegustot lietotaja datus', auth_user.name, token);
        });
        fetchUserImages();
    }
    axios.get(`http://localhost:2000/api/images`).then((response) => {
      console.log(response.data);  
      const imagesWithUrls = response.data.map((image) => ({
          ...image,
          url: `http://localhost:2000/storage/images/${image.filename}`, // Replace with the base URL of your file server
          author_profile_picture_path: `${image.user.profile_picture_path}`,
          author_name: `${image.user.name}`,
          apraksts: image.apraksts,
        }));
        setGalleryImages(imagesWithUrls);
        // save_log('Iegutas bildes', token);
      })
      .catch((error) => {
      });

      axios.get('http://localhost:2000/api/users').then(response => {setUsers(response.data);}).catch(error => {console.error(error);});

      setLoading(false);
      
  }, [token]);


  const handleLike = (imageId) => {
    if (token) {
      axios.post(`http://localhost:2000/api/images/${imageId}/like`, {}, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then(response => {
        const updatedGalleryImages = galleryImages.map((image) => {
          if (image.id === imageId) {
            return { ...image, likes: response.data };
          }
          return image;
        });
        setGalleryImages(updatedGalleryImages);
        save_log('Nolaikota bilde ar id ' + imageId, token);
        fetchUserImages();
        console.log('Image liked successfully:', response.data);

        // axios.get('http://localhost:2000/api/users').then(response => {setUsers(response.data);}).catch(error => {console.error(error);});
        // window.location.reload();
      })
      .catch(error => {
        save_log('Neizdevas nolaikot bildi ar id ' + imageId, token);
        console.error('Error liking image:', error);
      });
    } else {
      console.error('Neautentificets lietotajs');
    }
  };

  const handleDislike = (imageId) => {
    if (token) {
      axios.post(`http://localhost:2000/api/images/${imageId}/dislike`, {}, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then(response => {
        const updatedGalleryImages = galleryImages.map((image) => {
          if (image.id === imageId) {
            return { ...image, likes: response.data };
          }
          return image;
        });
        setGalleryImages(updatedGalleryImages);
        save_log('Dislaikota bilde ar id ' + imageId, token);
        fetchUserImages();
      })
      .catch(error => {
        console.error('Error disliking image:', error);
        save_log('Neizdevas dislaikot bildi ar id ' + imageId, token);  
      });
    } else {
      console.error('Neautentificēts lietotājs');
    }
  };

  const handleDelete = (imageId) => {
    axios
    .delete(`http://localhost:2000/api/images/${imageId}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      save_log('Bilde ar id ' + imageId + ' izdzesta!', token); 
      // console.log('Image deleted successfully:', response.data);
      fetchUserImages();
    })
    .catch((error) => {
      console.error('Error deleting image:', error);
    });
  };

  const [popupImage, setPopupImage] = useState(null);

  const handleView = (image, imageId) => {
    const x = image.id;
    console.log(image.id, imageId, x);
    if (token) { //  && image.author_name !== auth_user.name
      axios.post(`http://localhost:2000/api/images/${x}/view`, {}, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then(response => {
        const updatedGalleryImages = galleryImages.map((image2) => {
          if (image2.id === x) {
            return { ...image2, views: response.data };
          }
          return image2;
        });
        setGalleryImages(updatedGalleryImages);
        save_log('Bilde ar id ' + imageId + ' viewed!', token);
        fetchUserImages();
        setPopupImage(image);
      })
      .catch(error => {
        console.error('Error viewing image:', error);
      });
    } else {
        console.error('Neautentificets lietotajs');
    }
  };


  const handleClosePopup = () => {
    setPopupImage(null);
  };

  const handleSavePopup = (updatedDescription) => {
    const updatedGalleryImages = galleryImages.map((image) => {
      if (image.id === popupImage.id) {
        return { ...image, apraksts: updatedDescription };
      }
      return image;
    });
    setGalleryImages(updatedGalleryImages);
    fetchUserImages();
    handleClosePopup();
  };

  const handleImageUpload = (file) => {
    const isImage = file.type.startsWith('image/');
    const isSizeValid = file.size / 1024 / 1024 < 5;

    if (!isImage) {
      message.error('Drikst ielikt tikai bildes');
    } else if (!isSizeValid) {
      message.error('Mazākas par 5 MB...');
    } else {
      resizeImage(file).then((resizedImage) => {
      const formData = new FormData();
      formData.append('image', resizedImage);
        axios
        .post('http://localhost:2000/api/images', formData, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
        .then((response) => {
          save_log('Ielikta jauna bilde!', token); 
          message.success('Ielikta bilde!');
          fetchUserImages();
        })
        .catch((error) => {
          save_log('Neizdevas ielikt bildi', token);
        });

      }).catch((error) => {
        save_log('Neizdevas nomainit bildes izmeru', token);
        message.error('Nederigs izmers');
      });
    }
  };

  const resizeImage = (file) => {
    return new Promise((resolve, reject) => {
      const maxWidth = 800;
      const maxHeight = 800;
      const quality = 75;

      const reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onload = (event) => {
        const img = new Image();
        img.src = event.target.result;
        img.onload = () => {
          let width = img.width;
          let height = img.height;

          if (width > height) {
            if (width > maxWidth) {
              height *= maxWidth / width;
              width = maxWidth;
            }
          } else {
            if (height > maxHeight) {
              width *= maxHeight / height;
              height = maxHeight;
            }
          }

          const canvas = document.createElement('canvas');
          const ctx = canvas.getContext('2d');
          canvas.width = width;
          canvas.height = height;

          ctx.drawImage(img, 0, 0, width, height);

          canvas.toBlob((blob) => {
            const resizedFile = new File([blob], file.name, { type: file.type, lastModified: Date.now() });
            resolve(resizedFile);
          }, file.type, quality);
        };
        img.onerror = (error) => {
          reject(error);
        };
      };
      reader.onerror = (error) => {
        reject(error);
      };
    });
  };

  if (loading) return <Spin size="large" className='loading'/>;

  return (
    <div>
      <div style={{ display: 'flex', justifyContent: 'flex-end', padding: '16px' }}>
        <div className='main-container'>
          <div className='upload-images-container'>
            {(token &&
            <Upload
              accept="image/*"
              showUploadList={false}
              beforeUpload={handleImageUpload}
            >
              <Button icon={<UploadOutlined />} style={{ marginBottom: '16px' }}>
                Augšupielādēt attēlu
              </Button>
            </Upload>
            )}
          </div>
          <div className='user-images-container'>
            {(token && userImages.length > 0 && <>
            <h1>Jusu bildes</h1>
            <Row gutter={[50, 50]}>
              {userImages.map((image) => (
                <Col key={image.id} xs={12} sm={8} md={6} lg={4} xl={4} xxl={4}>
                  <Card
                    hoverable
                    cover={
                      <div className='square-image'>
                      <img alt={image.apraksts} src={image.url} style={{ borderRadius: '8px' }} onClick={() => handleView(image, image.id)} />
                      </div>
                      }
                    actions={[
                      <Space>
                        <Button icon={<DeleteOutlined />} onClick={() => handleDelete(image.id)} type="text" danger />
                        <Button icon={<LikeOutlined />} type="text"> {image.likes}</Button>
                        <Button className='skatijumi' icon={<EyeOutlined />} type="text"> {image.views} </Button>
                      </Space>
                    ]}
                  >
                    <Card.Meta
                      title={image.apraksts}
                    />
                  </Card>
                </Col>
              ))}
            </Row>
            </>
            )}
          </div>
          <div className='galery-container'>
            <div className='header-container'>
              {(token && <><h1>Citu lietotaju bildes</h1></>)}
              {(!token && <><h1>Galerija</h1></>)}
              <Button
                className='kartot_poga'
                onClick={handleSortBy}
                type="text"

              >
                {sortBy === 'views' ? <EyeOutlined /> : <CalendarOutlined />}
                {sortBy === 'views' ? 'Kārtot pēc popularitātes' : 'Kārtot ielikšanas secībā'}
              </Button>
            </div>
            <Row gutter={[50, 50]} style={{ marginRight: '20px' }}>
              {galleryImages
              .sort((imageA, imageB) => {
                if (sortBy === 'views') {
                  return imageB.views - imageA.views;
                } else if (sortBy === 'date') {
                  return imageA.id - imageB.id;
                }
                return 0;
              })
              .map((image, index) => (
                (!token || (auth_user && image.user.name !== auth_user.name)) && 
                <>
                {(index + 1) % 3 === 0 &&
                <Col key={index + 1000} xs={12} sm={8} md={6} lg={4} xl={4} xxl={4}>                
                  {token ? ( 
                  <Card hoverable
                    cover={
                      <div className='square-image'>
                        <img alt="bariba" src={bariba} onClick={() => window.location.href = "https://www.purina.lv/raksti/kaki/barosana-un-uzturs"} />
                      </div>
                      }>
                    <Card.Meta
                      avatar={<Avatar src={latvija} />}
                      description={`reklama`}
                    />
                  </Card>
                  
                  ) : (
                  <Card hoverable
                  cover={
                    <div className='square-image'>
                      <img alt="reklama" src={reklama} onClick={() => window.location.href = "http://www.dzd.lv/looking-for-home/en/"} />
                    </div>
                  }>
                    <Card.Meta
                      avatar={<Avatar src={latvija} />}
                      description={"reklama"}
                    />
                </Card>
                  )}
                </Col> }
                <Col key={image.id} xs={12} sm={8} md={6} lg={4} xl={4} xxl={4}>                
                  <Card hoverable
                    cover={
                      <div className='square-image'>
                        <img alt={image.apraksts} src={image.url} onClick={() => handleView(image)} />
                    </div>
                    }
                    actions={[
                      token ? (
                        <Space>
                          <Button icon={<LikeOutlined />} onClick={() => handleLike(image.id)} type="text"> {image.likes} </Button>
                          <Button icon={<DislikeOutlined />} onClick={() => handleDislike(image.id)} type="text" />
                          {auth_user.role === 'admin' &&
                          <Button icon={<DeleteOutlined />} onClick={() => handleDelete(image.id)} type="text" danger />
                          }
                          <Button className='skatijumi' icon={<EyeOutlined />} type="text"> {image.views} </Button>
                          </Space>
                      ) : null
                    ]}
                  >
                    {image.author_profile_picture_path !== 'none'  ? (
                    <Card.Meta
                    avatar={<Avatar src={'http://localhost:2000/storage/images/' + image.author_profile_picture_path} />}
                    title={image.apraksts}
                    description={`Autors: ${image.author_name}`}
                    />
                    ) : (
                    <Card.Meta
                      avatar={<Avatar/>}
                      title={image.apraksts}
                      description={`Autors: ${image.author_name}`}
                    />
                  )}
                  </Card>
                </Col>
                </>
              ))}
            </Row>
          </div>
        </div>
        {(token &&
        <div style={{ maxWidth: '300px' }}>
          <h1>Lietotāju saraksts</h1>
          <div style={{ border: '1px solid #d9d9d9', borderRadius: '4px', padding: '8px' }}>
            {users.map((user) => (
              <Card key={user.id} style={{ marginBottom: '16px' }}>
                {/* <Card.Meta avatar={<Avatar src={'http://localhost:2000/storage/images/' + user.profile_picture_path} />} title={user.name} /> */}
                {user.profile_picture_path !== 'none'  ? (
                  <Card.Meta avatar={<Avatar src={'http://localhost:2000/storage/images/' + user.profile_picture_path} />} title={user.name} />
                ) : (
                  <Card.Meta avatar={<Avatar/>} title={user.name} />
                )}
              </Card>
            ))}
          </div>
        </div>
        )}
      </div>
      {popupImage && (
        <ImagePopup image={popupImage} onClose={handleClosePopup} onSave={handleSavePopup} token={token} auth_user={auth_user}/>
      )}
    </div>
  );
};

export default Home;

