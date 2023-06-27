import React, { useState } from 'react';
import { Modal, Input, Button } from 'antd';
import axios from 'axios';
import save_log from './logService';

const ImagePopup = ({ image, onClose, onSave, token }) => {
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

  return (
    <Modal visible={true} centered footer={null} onCancel={onClose}>
      <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center' }}>
        <img alt={image.apraksts} src={image.url} style={{ maxWidth: '100%', maxHeight: '80vh' }} />
        <div style={{ marginTop: '16px' }}>
          <Input.TextArea value={description} onChange={handleDescriptionChange} rows={4} />
        </div>
        <div style={{ marginTop: '16px' }}>
          <Button type="primary" onClick={handleSave}>
            Saglabāt
          </Button>
        </div>
      </div>
    </Modal>
  );
};

export default ImagePopup;
