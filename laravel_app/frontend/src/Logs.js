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
      className: 'custom-cell',
    },
    {
      title: 'Laiks',
      dataIndex: 'timestamp',
      key: 'timestamp',
      className: 'custom-cell',
      render: (timestamp) => {
        const date = new Date(timestamp);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const seconds = String(date.getSeconds()).padStart(2, '0');
        const formattedTimestamp = `${day}.${month}.${year} ${hours}:${minutes}:${seconds}`;
        return <span className='formatted-timestamp'>{formattedTimestamp}</span>;
      },
    },
    {
      title: 'Ieraksts',
      dataIndex: 'error',
      key: 'error',
      className: 'custom-cell',
    },
    {
      title: 'Lietotajs',
      dataIndex: 'user',
      key: 'user',
      className: 'custom-cell',
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
