import React from 'react';
import { Form, Input, Button } from 'antd';

const CustomForm = () => {
  const onFinish = (values) => {
    // Handle form submission
    console.log('Received values:', values);
  };

  return (
    <Form name="custom-form" onFinish={onFinish}>
      <Form.Item
        label="Izvelies bildi"
        name="image"
        rules={[{ required: true, message: 'Please select an image!' }]}
      >
        <Input type="file" />
      </Form.Item>

      <Form.Item
        label="Ieraksti aprakstu"
        name="description"
        rules={[{ required: true, message: 'Please enter a description!' }]}
      >
        <Input.TextArea rows={1} />
      </Form.Item>

      <Form.Item>
        <Button type="primary" htmlType="submit">
          Ielikt!
        </Button>
      </Form.Item>
    </Form>
  );
};

export default CustomForm;
