import axios from 'axios';

const getCsrfToken = async () => {
  try {
    const response = await axios.get('http://localhost/api/csrf-token');
    const csrfToken = response.data.csrf_token;

    // Set the CSRF token for all Axios requests
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
  } catch (error) {
    console.error('Error retrieving CSRF token:', error);
  }
};

export default getCsrfToken;
