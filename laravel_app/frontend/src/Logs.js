import React, { useEffect, useState } from 'react';
import { Pagination, Table, Button, message , Popconfirm} from 'antd';
import axios from 'axios';

import './Logs.css';

const Logs = ( {token} ) => {
  const [logsData, setLogsData] = useState([]);
  

  useEffect(() => {
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
  }, [token]);

  const del_logs = () => {
    if (token) {
      axios
        .delete('http://localhost:2000/api/logs', {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
        .then(() => {
          message.success('All logs deleted successfully.');
        })
        .catch((error) => {
          message.error('Failed to delete logs. Please try again later.');
        });
    }
  };

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
    <div>
      <h1 className="logs-heading">Ieraksti
        <Popconfirm
            title="Are you sure you want to delete all logs?"
            onConfirm={del_logs}
            okText="Yes"
            cancelText="No"
            className="delete-confirm"
          >
        <Button type="danger" className="delete-button">Izdzest visus ierakstus</Button>
        </Popconfirm>
        </h1>
      <Table dataSource={logsData} columns={columns} scroll={{y: 40000}} pagination={false} />
    </div>
  );
};

export default Logs;
