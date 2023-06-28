import React, { useEffect, useState } from 'react';
import { Pagination, Table, Button, message , Popconfirm} from 'antd';
import axios from 'axios';

import './Logs.css';

const Logs = ( {token} ) => {
  const [logsData, setLogsData] = useState([]);
  
  const get_logs = () => {
    if (token) {
      axios.get(`http://localhost:2000/api/logs`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then((response) => {
        const logs = response.data.map((log, index) => ({
          ...log,
          key: index + 1,
        })).reverse();
        setLogsData(logs);
      });
    }
  }

  useEffect(() => {
    get_logs();
  }, [token]);

  const del_logs = () => {
    if (token) {
      axios
        .delete('http://localhost:2000/api/logs', {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
        .then((response) => {
          message.success('Izdzesti ' + response.data.skaits + ' ieraksti.');
          get_logs();
        })
        .catch((error) => {
          message.error('Neizdevās izdzēst ierakstus.');
        });
    }
  };

  const cleanup_logs = () => {
    if (token) {
      axios
        .delete('http://localhost:2000/api/logs_half', {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
        .then((response) => {
          message.success('Izdzesti ' + response.data.skaits + ' ieraksti.');
          get_logs();
        })
        .catch((error) => {
          message.error('Neizdevās  izdzēšana.');
        });
    }
  }

  const columns = [
    {
      title: 'ID',
      dataIndex: 'key',
      key: 'key',
    },
    {
      title: 'Timestamp',
      dataIndex: 'timestamp',
      key: 'timestamp',
    },
    {
      title: 'Kas notika',
      dataIndex: 'error',
      key: 'error',
    },
    {
      title: 'Lietotajs',
      dataIndex: 'user',
      key: 'user',
    },
  ];

  return (
    <div className='logs-container'>
      <h1 className="logs-heading">
        <span className='logs-heading-title'>Ieraksti</span>
        <div>
        <Button type="danger" className="delete-button" onClick={cleanup_logs}>Atstāt 50 jaunākos ierakstus</Button>
        <Popconfirm
            title="Vai tiešām vēlies atbrīvoties no visiem ierakstiem?"
            onConfirm={del_logs}
            okText="Jā"
            cancelText="Nē"
            className="delete-confirm"
          >
          <Button type="danger" className="delete-button">Izdzest visus ierakstus</Button>
        </Popconfirm>
        </div>
      </h1>
      <Table dataSource={logsData} columns={columns} scroll={{y: 40000}} pagination={false} />
    </div>
  );
};

export default Logs;
