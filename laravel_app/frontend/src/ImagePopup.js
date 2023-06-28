import React, { useState, useEffect} from 'react';
import { Modal, Input, Button } from 'antd';
import axios from 'axios';
import save_log from './logService';
import './App.css';

const ImagePopup = ({ image, onClose, onSave, token, auth_user}) => {
  const [description, setDescription] = useState(image.apraksts);

  const handleSave = () => {
    const imgDesc = image.apraksts;

    axios.put(`http://localhost:2000/api/images/${image.id}/update_desc`, { description }, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      
      console.log('desc uploaded successfully:', response.data);
      save_log('Nomainīts bildes ar id ' + image.id +' apraksts', token);
    })
    .catch((error) => {
      console.error('Failed to upload desc:', error);
      // Handle error if needed
    });
    onSave(description);
    onClose();
  };

  const handleDescriptionChange = (e) => {
    setDescription(e.target.value);
  };
  const [contentLoaded, setContentLoaded] = useState(false);
  const handlePopupClick = () => {
    setContentLoaded(true);
  };
  return (
    <Modal open={true} centered footer={null} onCancel={onClose}>
      <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center' }}>
        <img alt={image.apraksts} src={image.url} style={{ maxWidth: '100%', maxHeight: '80vh' }} />
        {(token && auth_user && (auth_user.role === 'admin' || auth_user.name === image.author_name)) ? (
        <>
        <div className="my-container">
          <Input.TextArea
            value={description}
            onChange={handleDescriptionChange}
            rows={4}
            className="my-text-area"
          />
          <div className="my-button-container">
            <Button type="primary" onClick={handleSave} className="my-button">
              Saglabāt
            </Button>
          </div>
        </div>
        </>) : (
        <div className={`no-permission-popup ${contentLoaded ? 'loaded' : ''}`} onClick={handlePopupClick}>
          <span>Bildes apraksts</span>
          <div className={`content ${contentLoaded ? 'loaded' : ''}`}>{image.apraksts}</div>
        </div>
        )}
      </div>
    </Modal>
  );
};

export default ImagePopup;
